import store from './store.js';

import './step-lib.js';
import './step-configuration.js';

export default class StepConfiguratorContainer extends HTMLElement {
    #pipeline = null;
    #listeners = [];

    connectedCallback() {
        this.render();
        this.#pipeline = this.querySelector('step-configuration');
        this.#bindEvents();
    }

    disconnectedCallback() {
        this.#unbindEvents();
    }

    // ─── Rendu ───────────────────────────────────────────────────────────────

    render() {
        this.innerHTML = `
            <section class="configurator-container ui grid">
                <div class="configurator-step-config-container five wide column" style="overflow-y:auto">
                    <step-lib></step-lib>
                </div>
                <div class="configurator-steps-container eleven wide column" style="overflow-y:auto">
                    <step-configuration></step-configuration>
                </div>
            </section>
        `;
    }

    // ─── Orchestration des événements ────────────────────────────────────────

    #bindEvents() {
        this.#unbindEvents();

        const onDragStart = () => this.#pipeline?.setDragging(true);
        const onDragEnd   = () => this.#pipeline?.setDragging(false);
        const onChange    = () => this.#saveChanges();

        this.addEventListener('step-lib:dragstart', onDragStart);
        this.addEventListener('step-lib:dragend', onDragEnd);
        this.addEventListener('step-configuration:change', onChange);

        this.#listeners.push(
            { element: this, type: 'step-lib:dragstart', listener: onDragStart },
            { element: this, type: 'step-lib:dragend', listener: onDragEnd },
            { element: this, type: 'step-configuration:change', listener: onChange },
        );
    }

    #unbindEvents() {
        this.#listeners.forEach(({ element, type, listener }) =>
            element.removeEventListener(type, listener)
        );
        this.#listeners = [];
    }

    // ─── Sauvegarde ──────────────────────────────────────────────────────────

    #saveChanges() {
        const counts = {};

        store.steps.forEach(step => {
            counts[step.code] = (counts[step.code] ?? 0) + 1;

            if (counts[step.code] > 1) {
                step.name = `${step.code}-${counts[step.code]}`;
            } else {
                delete step.name;
            }
        });

        this.dispatchEvent(new CustomEvent('change', { bubbles: false }));
    }
}

customElements.define('step-configurator-container', StepConfiguratorContainer);
