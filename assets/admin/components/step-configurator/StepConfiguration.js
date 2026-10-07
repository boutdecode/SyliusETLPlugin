import React from 'react';
import html from '../../html.js';
import { PlusIcon, ChevronUpIcon, ChevronDownIcon, ChevronRightIcon, TrashIcon } from '../../icons.js';
import { getStepFields, SchemaField } from './schema-fields.js';

function DropZone({ order, onDrop }) {
    const [dragOver, setDragOver] = React.useState(false);

    return html`
        <div
            className="drop-zone text-center text-muted border rounded p-2 my-2${dragOver ? ' drag-over' : ''}"
            onDragOver=${(e) => { e.preventDefault(); setDragOver(true); }}
            onDragLeave=${() => setDragOver(false)}
            onDrop=${(e) => {
                e.preventDefault();
                setDragOver(false);
                const code = e.dataTransfer.getData('text/plain');
                if (code) onDrop(order, code);
            }}
        >
            <small><${PlusIcon} /></small>
        </div>
    `;
}

function StepCard({ step, index, stepsCount, stepConfiguration, onMoveUp, onMoveDown, onRemove, onConfigChange }) {
    const [open, setOpen] = React.useState(false);
    const fields = getStepFields(step, stepConfiguration);

    return html`
        <div className="card mb-2">
            <div className="card-body">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        #${index + 1} - <strong>${step.name ?? step.code}</strong>
                    </div>
                    <div>
                        <div className="configurator-steps-buttons btn-group btn-group-sm">
                            <button
                                type="button"
                                className="btn btn-icon"
                                disabled=${index === 0}
                                title="Monter"
                                onClick=${() => onMoveUp(index)}
                            >
                                <${ChevronUpIcon} />
                            </button>
                            <button
                                type="button"
                                className="btn btn-icon"
                                disabled=${index === stepsCount - 1}
                                title="Descendre"
                                onClick=${() => onMoveDown(index)}
                            >
                                <${ChevronDownIcon} />
                            </button>
                            <button
                                type="button"
                                className="btn btn-icon btn-outline-danger"
                                title="Supprimer"
                                onClick=${() => onRemove(index)}
                            >
                                <${TrashIcon} />
                            </button>
                        </div>
                    </div>
                </div>

                <div className="mt-2">
                    <div className="d-flex align-items-center gap-1 text-muted" style=${{ cursor: 'pointer' }} onClick=${() => setOpen(!open)}>
                        ${open ? html`<${ChevronDownIcon} />` : html`<${ChevronRightIcon} />`}
                        <small>Configuration</small>
                    </div>
                    <div className="step-configuration-inputs mt-2" style=${{ display: open ? 'block' : 'none' }}>
                        ${fields.map((field) => html`
                            <${SchemaField}
                                key=${field.key}
                                idPrefix="field-${index}-${field.key}"
                                field=${field}
                                onChange=${(value) => onConfigChange(index, field.key, value)}
                            />
                        `)}
                    </div>
                </div>
            </div>
        </div>
    `;
}

export default function StepConfiguration({ steps, stepConfiguration, isDragging, onDrop, onMoveUp, onMoveDown, onRemove, onConfigChange }) {
    return html`
        <div className="step-configuration-steps${isDragging ? ' dragging' : ''}">
            <${DropZone} order=${0} onDrop=${onDrop} />
            ${steps.map((step, index) => html`
                <${React.Fragment} key=${`${step.code}-${index}`}>
                    <${StepCard}
                        step=${step}
                        index=${index}
                        stepsCount=${steps.length}
                        stepConfiguration=${stepConfiguration}
                        onMoveUp=${onMoveUp}
                        onMoveDown=${onMoveDown}
                        onRemove=${onRemove}
                        onConfigChange=${onConfigChange}
                    />
                    <${DropZone} order=${index + 1} onDrop=${onDrop} />
                <//>
            `)}
        </div>
    `;
}
