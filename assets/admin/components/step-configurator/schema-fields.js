import React from 'react';
import html from '../../html.js';

function normalizeTypes(fieldSchema) {
    if (!fieldSchema || typeof fieldSchema.type !== 'string') return [];
    return fieldSchema.type.split('|').map((t) => t.trim());
}

export function getStepFields(step, stepConfiguration) {
    const stepConfig = stepConfiguration.find((c) => c.code === step.code);
    if (!stepConfig) return [];

    const descriptions = stepConfig.configuration_description ?? {};
    const schemas = stepConfig.configuration_schema ?? {};
    const keys = Array.from(new Set([...Object.keys(schemas), ...Object.keys(descriptions)]));

    return keys.map((key) => ({
        key,
        value: step.configuration[key],
        help: descriptions[key] ?? '',
        fieldSchema: schemas[key] ?? null,
    }));
}

function HelpIcon({ text }) {
    const [visible, setVisible] = React.useState(false);

    if (!text) return '';

    return html`
        <span
            className="field-help-icon"
            onMouseEnter=${() => setVisible(true)}
            onMouseLeave=${() => setVisible(false)}
        >
            <i className="question circle outline icon"></i>
            ${visible ? html`<span className="field-help-popover">${text}</span>` : ''}
        </span>
    `;
}

function FieldLabel({ id, text, help, required }) {
    return html`
        <div style=${{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
            <label htmlFor=${id} style=${{ fontWeight: 'bold' }}>
                ${text}${required ? ' *' : ''}
            </label>
            <${HelpIcon} text=${help} />
        </div>
    `;
}

function TextField({ id, field, onChange }) {
    return html`
        <input
            type="text"
            name=${field.key}
            defaultValue=${field.value ?? ''}
            id=${id}
            onBlur=${(e) => onChange(e.target.value === '' ? undefined : e.target.value)}
        />
    `;
}

function NumberField({ id, field, integer, onChange }) {
    return html`
        <input
            type="number"
            step=${integer ? '1' : 'any'}
            name=${field.key}
            defaultValue=${field.value ?? ''}
            id=${id}
            onBlur=${(e) => {
                if (e.target.value === '') { onChange(undefined); return; }
                const num = Number(e.target.value);
                onChange(Number.isNaN(num) ? undefined : num);
            }}
        />
    `;
}

function BooleanField({ id, field, onChange }) {
    return html`
        <div className="ui checkbox">
            <input
                type="checkbox"
                name=${field.key}
                defaultChecked=${field.value === true}
                id=${id}
                onChange=${(e) => onChange(e.target.checked)}
            />
            <label htmlFor=${id}></label>
        </div>
    `;
}

function EnumField({ id, field, enumValues, onChange }) {
    const current = field.value;

    return html`
        <select
            name=${field.key}
            id=${id}
            defaultValue=${current === undefined ? '' : String(current)}
            onChange=${(e) => {
                const raw = e.target.value;
                if (raw === '') { onChange(undefined); return; }
                const match = enumValues.find((v) => String(v) === raw);
                onChange(match !== undefined ? match : raw);
            }}
        >
            <option value="">--</option>
            ${enumValues.map((v) => html`<option key=${String(v)} value=${String(v)}>${String(v)}</option>`)}
        </select>
    `;
}

function JsonField({ id, field, onChange }) {
    const initial = field.value === undefined ? '' : JSON.stringify(field.value, null, 2);
    const [text, setText] = React.useState(initial);
    const [error, setError] = React.useState(null);

    return html`
        <textarea
            name=${field.key}
            id=${id}
            rows="4"
            defaultValue=${initial}
            onChange=${(e) => setText(e.target.value)}
            onBlur=${() => {
                if (text.trim() === '') { setError(null); onChange(undefined); return; }
                try {
                    const parsed = JSON.parse(text);
                    setError(null);
                    onChange(parsed);
                } catch {
                    setError('JSON invalide');
                }
            }}
        ></textarea>
        ${error ? html`<small className="ui red pointing label">${error}</small>` : ''}
    `;
}

function NestedObjectField({ idPrefix, field, onChange }) {
    const value = field.value && typeof field.value === 'object' && !Array.isArray(field.value) ? field.value : {};
    const schema = field.fieldSchema.schema ?? {};

    const handleChildChange = (key, childValue) => {
        const next = { ...value };
        if (childValue === undefined) {
            delete next[key];
        } else {
            next[key] = childValue;
        }
        onChange(next);
    };

    return html`
        <div className="nested-schema-fields" style=${{ paddingLeft: '1em', borderLeft: '2px solid rgba(0,0,0,0.1)' }}>
            ${Object.entries(schema).map(([key, childSchema]) => html`
                <${SchemaField}
                    key=${key}
                    idPrefix="${idPrefix}-${key}"
                    field=${{ key, value: value[key], help: '', fieldSchema: childSchema }}
                    onChange=${(v) => handleChildChange(key, v)}
                />
            `)}
        </div>
    `;
}

function RepeatableArrayField({ idPrefix, field, onChange }) {
    const items = Array.isArray(field.value) ? field.value : [];
    const properties = field.fieldSchema.properties ?? {};

    const updateItem = (index, next) => {
        const nextItems = [...items];
        nextItems[index] = next;
        onChange(nextItems);
    };

    const removeItem = (index) => {
        const nextItems = [...items];
        nextItems.splice(index, 1);
        onChange(nextItems);
    };

    return html`
        <div className="repeatable-schema-fields">
            ${items.map((item, index) => html`
                <div key=${index} className="ui segment" style=${{ position: 'relative' }}>
                    <button
                        type="button"
                        className="ui mini red icon button"
                        style=${{ position: 'absolute', top: '0.5em', right: '0.5em' }}
                        onClick=${() => removeItem(index)}
                    >
                        <i className="icon trash alternate"></i>
                    </button>
                    ${Object.entries(properties).map(([key, childSchema]) => html`
                        <${SchemaField}
                            key=${key}
                            idPrefix="${idPrefix}-${index}-${key}"
                            field=${{ key, value: item?.[key], help: '', fieldSchema: childSchema }}
                            onChange=${(v) => {
                                const nextItem = { ...item };
                                if (v === undefined) { delete nextItem[key]; } else { nextItem[key] = v; }
                                updateItem(index, nextItem);
                            }}
                        />
                    `)}
                </div>
            `)}
            <button type="button" className="ui mini button" onClick=${() => onChange([...items, {}])}>
                <i className="icon plus"></i> Ajouter
            </button>
        </div>
    `;
}

export function SchemaField({ idPrefix, field, onChange }) {
    const { fieldSchema } = field;
    const types = normalizeTypes(fieldSchema);
    const required = fieldSchema?.required === true;
    const isCheckbox = types.includes('boolean') || types.includes('bool');
    const enumValues = Array.isArray(fieldSchema?.enum) && fieldSchema.enum.length > 0 ? fieldSchema.enum : null;

    let control;
    if (enumValues) {
        control = html`<${EnumField} id=${idPrefix} field=${field} enumValues=${enumValues} onChange=${onChange} />`;
    } else if (!fieldSchema || types.length === 0) {
        control = html`<${TextField} id=${idPrefix} field=${field} onChange=${onChange} />`;
    } else if (isCheckbox) {
        control = html`<${BooleanField} id=${idPrefix} field=${field} onChange=${onChange} />`;
    } else if ((types.includes('integer') || types.includes('int')) && !types.includes('string')) {
        control = html`<${NumberField} id=${idPrefix} field=${field} integer=${true} onChange=${onChange} />`;
    } else if (types.includes('number') && !types.includes('string')) {
        control = html`<${NumberField} id=${idPrefix} field=${field} integer=${false} onChange=${onChange} />`;
    } else if (types.includes('object') && fieldSchema.schema) {
        control = html`<${NestedObjectField} idPrefix=${idPrefix} field=${field} onChange=${onChange} />`;
    } else if (types.includes('object')) {
        control = html`<${JsonField} id=${idPrefix} field=${field} onChange=${onChange} />`;
    } else if (types.includes('array') && fieldSchema.properties) {
        control = html`<${RepeatableArrayField} idPrefix=${idPrefix} field=${field} onChange=${onChange} />`;
    } else if (types.includes('array')) {
        control = html`<${JsonField} id=${idPrefix} field=${field} onChange=${onChange} />`;
    } else {
        control = html`<${TextField} id=${idPrefix} field=${field} onChange=${onChange} />`;
    }

    return html`
        <div className="ui field" style=${{ marginBottom: '0.5em' }}>
            <${FieldLabel} id=${idPrefix} text=${field.key} help=${field.help} required=${required} />
            ${control}
        </div>
    `;
}
