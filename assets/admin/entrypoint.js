import './styles/configurator.css';

import { createRoot } from 'react-dom/client';
import html from './html.js';
import StepConfiguratorContainer from './components/step-configurator/StepConfiguratorContainer.js';
import StepConfigurationOverrideConfigurator from './components/step-configuration-override/StepConfigurationOverrideConfigurator.js';
import PipelineInputSelector from './components/pipeline-input/PipelineInputSelector.js';

function detectPipelineInputType(value) {
    if (!value || value === 'null' || value === '[]' || value === '{}') {
        return 'json';
    }

    try {
        const parsed = JSON.parse(value);
        if (parsed && typeof parsed === 'object' && parsed._file_path) {
            return 'file';
        }
        return 'json';
    } catch {
        return 'text';
    }
}

function mountStepConfigurator(element) {
    const stepConfiguration = JSON.parse(element.dataset.configuration);
    const initialSteps = JSON.parse(element.textContent);

    element.style.display = 'none';

    const mountPoint = document.createElement('div');
    element.after(mountPoint);

    createRoot(mountPoint).render(html`
        <${StepConfiguratorContainer}
            initialSteps=${initialSteps}
            stepConfiguration=${stepConfiguration}
            onChange=${(steps) => { element.textContent = JSON.stringify(steps); }}
        />
    `);
}

function mountStepConfigurationOverride(element) {
    const stepConfiguration = JSON.parse(element.dataset.configuration);
    const steps = JSON.parse(element.textContent);

    element.style.display = 'none';
    element.textContent = '[]';

    const mountPoint = document.createElement('div');
    element.after(mountPoint);

    createRoot(mountPoint).render(html`
        <${StepConfigurationOverrideConfigurator}
            steps=${steps}
            stepConfiguration=${stepConfiguration}
            onChange=${(override) => { element.textContent = JSON.stringify(override); }}
        />
    `);
}

function mountPipelineInput(element) {
    const initialType = detectPipelineInputType(element.value ?? '');
    const fileFieldId = element.dataset.fileFieldId;
    const textareaRow = element.closest('.field') ?? element.parentElement;

    const mountPoint = document.createElement('div');
    textareaRow.before(mountPoint);

    createRoot(mountPoint).render(html`
        <${PipelineInputSelector}
            initialType=${initialType}
            textareaId=${element.id}
            fileFieldId=${fileFieldId}
        />
    `);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-controller="step-configurator"]').forEach(mountStepConfigurator);
    document.querySelectorAll('[data-controller="step-configuration-override"]').forEach(mountStepConfigurationOverride);
    document.querySelectorAll('[data-controller="pipeline-input"]').forEach(mountPipelineInput);
});
