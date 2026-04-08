import store from './store.js';

export default class StepConfigurationOverride extends HTMLElement {

    #listeners = [];

    connectedCallback() {
        this.render();
        this.#bindEvents();
    }

    disconnectedCallback() {
        this.#unbindEvents();
    }

    // ─── API publique ────────────────────────────────────────────────────────

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
            ${store.steps.map((step, index) => `
                ${this.#renderStepCard(step, index)}
            `).join('')}
        `;
    }

    #renderStepCard(step, index) {
        const configs = this.#getStepFieldConfigs(step);

        return `
            <div class="ui card fluid">
                <div class="content">
                    <header class="ui">
                        #${index + 1} - <strong>${step.name ?? step.code}</strong>
                    </header>

                    <div class="ui accordion">
                        <div class="title">
                            <i class="dropdown icon"></i>
                            <small>Configuration</small>
                        </div>
                        <div class="step-configuration-override-inputs content">
                            ${configs.map(field => `
                                <div class="ui field">
                                    <label for="field-${index}-${field.key}">${field.key}</label>
                                    <div class="ui input">
                                        <input
                                            type="text"
                                            name="${field.key}"
                                            value="${this.#escapeAttr(field.value)}"
                                            data-index="${index}"
                                            id="field-${index}-${field.key}"
                                        >
                                    </div>
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
        this.#bindConfigInputs();
    }

    #unbindEvents() {
        this.#listeners.forEach(({ element, type, listener }) =>
            element.removeEventListener(type, listener)
        );
        this.#listeners = [];
    }

    #bindConfigInputs() {
        this.querySelectorAll('.step-configuration-override-inputs input').forEach(input => {
            const onChange = () => {
                const index = parseInt(input.dataset.index, 10);
                if (!store.override[index]) {
                    store.override[index] = {
                        code: store.steps[index].code,
                        name: store.steps[index].name,
                        configuration: {},
                    }
                }

                store.override[index].configuration[input.name] = input.value;
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
        this.dispatchEvent(new CustomEvent('step-configuration-override:change', { bubbles: true }));
    }
}

customElements.define('step-configuration-override', StepConfigurationOverride);
