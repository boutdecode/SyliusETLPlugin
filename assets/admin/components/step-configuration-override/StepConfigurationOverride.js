import React from 'react';
import html from '../../html.js';
import { getStepFields, SchemaField } from '../step-configurator/schema-fields.js';

function StepCard({ step, index, stepConfiguration, onFieldChange }) {
    const [open, setOpen] = React.useState(false);
    const fields = getStepFields(step, stepConfiguration);

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
                        ${fields.map((field) => html`
                            <${SchemaField}
                                key=${field.key}
                                idPrefix="field-${index}-${field.key}"
                                field=${field}
                                onChange=${(value) => onFieldChange(index, field.key, value)}
                            />
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
