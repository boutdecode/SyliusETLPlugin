import store from './store.js';

export default class StepConfiguration extends HTMLElement {

    #listeners = [];

    connectedCallback() {
        this.render();
        this.#bindEvents();
    }

    disconnectedCallback() {
        this.#unbindEvents();
    }

    refresh() {
        this.render();
        this.#bindEvents();

        $('.ui.accordion').accordion();
    }

    setDragging(active) {
        this.classList.toggle('dragging', active);
    }

    // ─── Rendu ───────────────────────────────────────────────────────────────

    render() {
        this.innerHTML = `
            ${this.#renderDropZone(0)}
            ${store.steps.map((step, index) => `
                ${this.#renderStepCard(step, index)}
                ${this.#renderDropZone(index + 1)}
            `).join('')}
        `;
    }

    #renderDropZone(order) {
        return `
            <div
                class="drop-zone ui segment center aligned"
                data-order="${order}"
            >
                <small class="ui text disabled">
                    <i class="icon plus"></i>
                </small>
            </div>
        `;
    }

    #renderStepCard(step, index) {
        const configs = this.#getStepFieldConfigs(step);

        return `
            <div class="ui card fluid">
                <div class="content">
                    <div class="ui grid">
                        <div class="twelve wide column middle aligned">
                            #${index + 1} - <strong>${step.name ?? step.code}</strong>
                        </div>
                        <div class="four wide column right aligned">
                            <div class="configurator-steps-buttons ui icon buttons mini">
                                <button type="button" class="ui button"
                                    data-action="moveUp" data-index="${index}"
                                    ${index === 0 ? 'disabled' : ''}
                                    title="Monter"
                                >
                                    <i class="icon angle up"></i>
                                </button>
                                <button type="button" class="ui button"
                                    data-action="moveDown" data-index="${index}"
                                    ${index === store.steps.length - 1 ? 'disabled' : ''}
                                    title="Descendre"
                                >
                                    <i class="icon angle down"></i>
                                </button>
                                <button type="button" class="ui red button"
                                    data-action="remove" data-index="${index}"
                                    title="Supprimer"
                                >
                                    <i class="icon trash alternate" style="stroke-color: white;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="ui accordion">
                        <div class="title">
                            <i class="dropdown icon"></i>
                            <small>Configuration</small>
                        </div>
                        <div class="content step-configuration-inputs">
                            ${configs.map(field => `
                                <div class="ui field" style="margin-bottom:0.5em">
                                    <label for="field-${index}-${field.key}">${field.key}</label>
                                    <input
                                        type="text"
                                        name="${field.key}"
                                        value="${this.#escapeAttr(field.value)}"
                                        data-index="${index}"
                                        id="field-${index}-${field.key}"
                                    >
                                    ${field.help ? `<small class="ui pointing label">${field.help}</small>` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // ─── Événements ──────────────────────────────────────────────────────────

    #bindEvents() {
        this.#unbindEvents();
        this.#bindDropZones();
        this.#bindStepButtons();
        this.#bindConfigInputs();
    }

    #unbindEvents() {
        this.#listeners.forEach(({ element, type, listener }) =>
            element.removeEventListener(type, listener)
        );
        this.#listeners = [];
    }

    #bindDropZones() {
        this.querySelectorAll('.drop-zone').forEach(zone => {
            const onDragOver  = (e) => { e.preventDefault(); zone.classList.add('drag-over'); };
            const onDragLeave = ()  => { zone.classList.remove('drag-over'); };
            const onDrop      = (e) => {
                e.preventDefault();
                zone.classList.remove('drag-over');

                const code       = e.dataTransfer.getData('text/plain');
                const stepConfig = store.stepConfiguration.find(c => c.code === code);
                if (!stepConfig) return;

                const index = parseInt(zone.dataset.order, 10);
                store.steps.splice(index, 0, { code: stepConfig.code, configuration: {} });
                this.#emitChange();
                this.refresh();
            };

            zone.addEventListener('dragover', onDragOver);
            zone.addEventListener('dragleave', onDragLeave);
            zone.addEventListener('drop', onDrop);

            this.#listeners.push(
                { element: zone, type: 'dragover', listener: onDragOver },
                { element: zone, type: 'dragleave', listener: onDragLeave },
                { element: zone, type: 'drop', listener: onDrop },
            );
        });
    }

    #bindStepButtons() {
        this.querySelectorAll('.configurator-steps-buttons').forEach(group => {
            const onClick = (e) => {
                const button = e.target.closest('button');
                if (!button) return;

                const action = button.dataset.action;
                const index  = parseInt(button.dataset.index, 10);

                if (action === 'moveUp' && index > 0) {
                    [store.steps[index - 1], store.steps[index]] = [store.steps[index], store.steps[index - 1]];
                    this.#emitChange();
                    this.refresh();
                }

                if (action === 'moveDown' && index < store.steps.length - 1) {
                    [store.steps[index + 1], store.steps[index]] = [store.steps[index], store.steps[index + 1]];
                    this.#emitChange();
                    this.refresh();
                }

                if (action === 'remove') {
                    store.steps.splice(index, 1);
                    this.#emitChange();
                    this.refresh();
                }
            };

            group.addEventListener('click', onClick);
            this.#listeners.push({ element: group, type: 'click', listener: onClick });
        });
    }

    #bindConfigInputs() {
        this.querySelectorAll('.step-configuration-inputs input').forEach(input => {
            const onChange = () => {
                const index = parseInt(input.dataset.index, 10);

                if (Array.isArray(store.steps[index].configuration)) {
                    store.steps[index].configuration = {};
                }

                if (input.value === '') {
                    delete store.steps[index].configuration[input.name];
                } else {
                    store.steps[index].configuration[input.name] = input.value;
                }

                this.#emitChange();
            };

            input.addEventListener('change', onChange);
            this.#listeners.push({ element: input, type: 'change', listener: onChange });
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    #getStepFieldConfigs(step) {
        const stepConfig = store.stepConfiguration.find(c => c.code === step.code);
        if (!stepConfig) return [];

        return Object.entries(stepConfig.configuration_description ?? {}).map(([key, help]) => ({
            key,
            value: step.configuration[key] ?? '',
            help:  help ?? '',
        }));
    }

    #escapeAttr(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    #emitChange() {
        this.dispatchEvent(new CustomEvent('step-configuration:change', { bubbles: true }));
    }
}

customElements.define('step-configuration', StepConfiguration);
