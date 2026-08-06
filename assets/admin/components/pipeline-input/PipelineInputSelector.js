import React from 'react';
import html from '../../html.js';

const INPUT_TYPES = ['text', 'file', 'json'];

const LABELS = {
    text: 'Texte',
    file: 'Fichier',
    json: 'JSON',
};

export default function PipelineInputSelector({ initialType, textareaId, fileFieldId }) {
    const [currentType, setCurrentType] = React.useState(initialType ?? 'json');

    React.useEffect(() => {
        const textarea = document.getElementById(textareaId);
        const fileWrapper = document.getElementById(fileFieldId);
        if (!textarea || !fileWrapper) return;

        const isFile = currentType === 'file';
        const textareaRow = textarea.closest('.field') ?? textarea.parentElement;
        if (textareaRow) textareaRow.style.display = isFile ? 'none' : '';

        fileWrapper.style.display = isFile ? '' : 'none';
    }, [currentType, textareaId, fileFieldId]);

    return html`
        <div className="ui secondary pointing menu pipeline-input-tabs">
            ${INPUT_TYPES.map((type) => html`
                <a
                    key=${type}
                    className="item${currentType === type ? ' active' : ''}"
                    onClick=${() => setCurrentType(type)}
                >
                    ${LABELS[type]}
                </a>
            `)}
        </div>
    `;
}
