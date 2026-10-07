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
        <ul className="nav nav-pills pipeline-input-tabs mb-2">
            ${INPUT_TYPES.map((type) => html`
                <li key=${type} className="nav-item">
                    <a
                        href="#"
                        className="nav-link${currentType === type ? ' active' : ''}"
                        onClick=${(e) => { e.preventDefault(); setCurrentType(type); }}
                    >
                        ${LABELS[type]}
                    </a>
                </li>
            `)}
        </ul>
    `;
}
