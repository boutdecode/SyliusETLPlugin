import store from './step-configuration-override/store.js';

import './step-configuration-override/configurator.js';

export default class StepConfiguratorOverrideController {
    constructor(element) {
        this.element = element;
    }

    initialize() {
        console.log(this.element);

        store.stepConfiguration = JSON.parse(this.element.dataset.configuration);
        store.steps = JSON.parse(this.element.textContent);

        this.element.style.display = 'none';
        this.element.textContent = '[]';

        const configuratorContainer = document.createElement('step-configuration-override-configurator');
        this.element.after(configuratorContainer);

        configuratorContainer.addEventListener('change', () => {
            this.element.textContent = JSON.stringify(store.override);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const stepConfiguratorOverrideElements = document.querySelectorAll('[data-controller="step-configuration-override"]');
    stepConfiguratorOverrideElements.forEach(element => {
        new StepConfiguratorOverrideController(element).initialize();
    });
});
