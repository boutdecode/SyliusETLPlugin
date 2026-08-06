import React from 'react';
import html from '../../html.js';
import { getStepFields, SchemaField } from './schema-fields.js';

function DropZone({ order, onDrop }) {
    const [dragOver, setDragOver] = React.useState(false);

    return html`
        <div
            className="drop-zone ui segment center aligned${dragOver ? ' drag-over' : ''}"
            onDragOver=${(e) => { e.preventDefault(); setDragOver(true); }}
            onDragLeave=${() => setDragOver(false)}
            onDrop=${(e) => {
                e.preventDefault();
                setDragOver(false);
                const code = e.dataTransfer.getData('text/plain');
                if (code) onDrop(order, code);
            }}
        >
            <small className="ui text disabled">
                <i className="icon plus"></i>
            </small>
        </div>
    `;
}

function StepCard({ step, index, stepsCount, stepConfiguration, onMoveUp, onMoveDown, onRemove, onConfigChange }) {
    const [open, setOpen] = React.useState(false);
    const fields = getStepFields(step, stepConfiguration);

    return html`
        <div className="ui card fluid">
            <div className="content">
                <div className="ui grid">
                    <div className="twelve wide column middle aligned">
                        #${index + 1} - <strong>${step.name ?? step.code}</strong>
                    </div>
                    <div className="four wide column right aligned">
                        <div className="configurator-steps-buttons ui icon buttons mini">
                            <button
                                type="button"
                                className="ui button"
                                disabled=${index === 0}
                                title="Monter"
                                onClick=${() => onMoveUp(index)}
                            >
                                <i className="icon angle up"></i>
                            </button>
                            <button
                                type="button"
                                className="ui button"
                                disabled=${index === stepsCount - 1}
                                title="Descendre"
                                onClick=${() => onMoveDown(index)}
                            >
                                <i className="icon angle down"></i>
                            </button>
                            <button
                                type="button"
                                className="ui red button"
                                title="Supprimer"
                                onClick=${() => onRemove(index)}
                            >
                                <i className="icon trash alternate" style=${{ strokeColor: 'white' }}></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div className="ui accordion">
                    <div className="title${open ? ' active' : ''}" onClick=${() => setOpen(!open)}>
                        <i className="dropdown icon"></i>
                        <small>Configuration</small>
                    </div>
                    <div className="content step-configuration-inputs${open ? ' active' : ''}" style=${{ display: open ? 'block' : 'none' }}>
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
