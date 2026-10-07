import React from 'react';
import html from '../../html.js';
import { ChevronDownIcon, ChevronRightIcon } from '../../icons.js';
import { getStepFields, SchemaField } from '../step-configurator/schema-fields.js';

function StepCard({ step, index, stepConfiguration, onFieldChange }) {
    const [open, setOpen] = React.useState(false);
    const fields = getStepFields(step, stepConfiguration);

    return html`
        <div className="card mb-2">
            <div className="card-body">
                <header>
                    #${index + 1} - <strong>${step.name ?? step.code}</strong>
                </header>

                <div className="mt-2">
                    <div className="d-flex align-items-center gap-1 text-muted" style=${{ cursor: 'pointer' }} onClick=${() => setOpen(!open)}>
                        ${open ? html`<${ChevronDownIcon} />` : html`<${ChevronRightIcon} />`}
                        <small>Configuration</small>
                    </div>
                    <div className="step-configuration-override-inputs mt-2" style=${{ display: open ? 'block' : 'none' }}>
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
