import './step-configurator/container.js';
import store from './step-configurator/store.js';

export default class StepConfiguratorController {
    constructor(element) {
        this.element = element;
    }

    initialize() {
        store.stepConfiguration = JSON.parse(this.element.dataset.configuration);
        store.steps = JSON.parse(this.element.textContent);

        this.element.style.display = 'none';

        const configuratorContainer = document.createElement('step-configurator-container');
        this.element.after(configuratorContainer);

        configuratorContainer.addEventListener('change', () => {
            this.element.textContent = JSON.stringify(store.steps);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const stepConfiguratorElements = document.querySelectorAll('[data-controller="step-configurator"]');
    stepConfiguratorElements.forEach(element => {
        new StepConfiguratorController(element).initialize();
    });
});
