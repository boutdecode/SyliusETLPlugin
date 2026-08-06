import React from 'react';
import html from '../../html.js';

function getStepFieldConfigs(step, stepConfiguration) {
    const stepConfig = stepConfiguration.find((c) => c.code === step.code);
    if (!stepConfig) return [];

    return Object.entries(stepConfig.configuration_description ?? {}).map(([key, help]) => ({
        key,
        value: step.configuration[key] ?? '',
        help: help ?? '',
    }));
}

function StepCard({ step, index, stepConfiguration, onFieldChange }) {
    const [open, setOpen] = React.useState(false);
    const configs = getStepFieldConfigs(step, stepConfiguration);

    return html`
        <div className="ui card fluid">
            <div className="content">
                <header className="ui">
                    #${index + 1} - <strong>${step.name ?? step.code}</strong>
                </header>

                <div className="ui accordion">
                    <div className="title${open ? ' active' : ''}" onClick=${() => setOpen(!open)}>
                        <i className="dropdown icon"></i>
                        <small>Configuration</small>
                    </div>
                    <div className="step-configuration-override-inputs content" style=${{ display: open ? 'block' : 'none' }}>
                        ${configs.map((field) => html`
                            <div key=${field.key} className="ui field">
                                <label htmlFor="field-${index}-${field.key}">${field.key}</label>
                                <div className="ui input">
                                    <input
                                        type="text"
                                        name=${field.key}
                                        defaultValue=${field.value}
                                        id="field-${index}-${field.key}"
                                        onBlur=${(e) => onFieldChange(index, field.key, e.target.value)}
                                    />
                                </div>
                                ${field.help ? html`<small className="ui pointing label">${field.help}</small>` : ''}
                            </div>
                        `)}
                    </div>
                </div>
            </div>
        </div>
    `;
}

export default function StepConfigurationOverride({ steps, stepConfiguration, onFieldChange }) {
    return html`
        <div>
            ${steps.map((step, index) => html`
                <${StepCard}
                    key=${`${step.code}-${index}`}
                    step=${step}
                    index=${index}
                    stepConfiguration=${stepConfiguration}
                    onFieldChange=${onFieldChange}
                />
            `)}
        </div>
    `;
}
