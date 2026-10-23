import React from 'react';
import html from '../../html.js';
import Icon from '../../icons.js';
import { getStepFields, SchemaField } from './schema-fields.js';

function DropZone({ order, onDrop }) {
    const [dragOver, setDragOver] = React.useState(false);

    return html`
        <div
            className="drop-zone card card-body text-center${dragOver ? ' drag-over' : ''}"
            onDragOver=${(e) => { e.preventDefault(); setDragOver(true); }}
            onDragLeave=${() => setDragOver(false)}
            onDrop=${(e) => {
                e.preventDefault();
                setDragOver(false);
                const code = e.dataTransfer.getData('text/plain');
                if (code) onDrop(order, code);
            }}
        >
            <small className="text-secondary">
                <${Icon} name="plus" />
            </small>
        </div>
    `;
}

function StepCard({ step, index, stepsCount, stepConfiguration, onMoveUp, onMoveDown, onRemove, onConfigChange }) {
    const [open, setOpen] = React.useState(false);
    const fields = getStepFields(step, stepConfiguration);

    return html`
        <div className="card mb-2">
            <div className="card-body">
                <div className="d-flex align-items-center justify-content-between">
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
                                <${Icon} name="up" />
                            </button>
                            <button
                                type="button"
                                className="btn btn-icon"
                                disabled=${index === stepsCount - 1}
                                title="Descendre"
                                onClick=${() => onMoveDown(index)}
                            >
                                <${Icon} name="down" />
                            </button>
                            <button
                                type="button"
                                className="btn btn-icon btn-danger"
                                title="Supprimer"
                                onClick=${() => onRemove(index)}
                            >
                                <${Icon} name="trash" />
                            </button>
                        </div>
                    </div>
                </div>

                <div className="mt-2">
                    <a href="#" className="text-secondary" onClick=${(e) => { e.preventDefault(); setOpen(!open); }}>
                        <${Icon} name=${open ? 'down' : 'chevron'} />
                        <small>Configuration</small>
                    </a>
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

export default function StepConfiguration({ steps, stepIds, stepConfiguration, isDragging, onDrop, onMoveUp, onMoveDown, onRemove, onConfigChange }) {
    return html`
        <div className="step-configuration-steps${isDragging ? ' dragging' : ''}">
            <${DropZone} order=${0} onDrop=${onDrop} />
            ${steps.map((step, index) => html`
                <${React.Fragment} key=${stepIds[index]}>
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
