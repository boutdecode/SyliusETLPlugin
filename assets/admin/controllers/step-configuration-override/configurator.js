import store from './store.js';

import './step-configuration-override.js';

export default class StepConfigurationOverrideConfigurator extends HTMLElement {

    #listeners = [];

    connectedCallback() {
        this.render();
        this.#bindEvents();
    }

    disconnectedCallback() {
        this.#unbindEvents();
    }

    // ─── Rendu ───────────────────────────────────────────────────────────────

    render() {
        this.innerHTML = `
            <section class="configuration-override-container ui grid">
                <div class="configuration-override-steps-container">
                    <step-configuration-override></step-configuration-override>
                </div>
            </section>
        `;
    }

    // ─── Orchestration des événements ────────────────────────────────────────

    #bindEvents() {
        this.#unbindEvents();

        const onChange    = () => this.#saveChanges();

        this.addEventListener('step-configuration-override:change', onChange);

        this.#listeners.push(
            { element: this, type: 'step-configuration-override:change', listener: onChange },
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
        store.override = store.override.filter(step => !!step);

        this.dispatchEvent(new CustomEvent('change', { bubbles: false }));
    }
}

customElements.define('step-configuration-override-configurator', StepConfigurationOverrideConfigurator);
