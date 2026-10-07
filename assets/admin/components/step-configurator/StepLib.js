import React from 'react';
import html from '../../html.js';

const CATEGORIES = ['extractor', 'transformer', 'loader'];

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function DragItem({ config, onDragStart, onDragEnd }) {
    const [dragging, setDragging] = React.useState(false);

    return html`
        <div
            className="list-group-item list-group-item-action drag-item${dragging ? ' dragging' : ''}"
            draggable="true"
            onDragStart=${(e) => {
                e.dataTransfer.setData('text/plain', config.code);
                setDragging(true);
                onDragStart(config.code);
            }}
            onDragEnd=${() => {
                setDragging(false);
                onDragEnd();
            }}
        >
            <div>
                <div className="fw-bold">${config.name}</div>
                <div className="text-muted fst-italic small">${config.code}</div>
                <div className="small">${config.description}</div>
            </div>
        </div>
    `;
}

export default function StepLib({ stepConfiguration, onDragStart, onDragEnd }) {
    const [activeCategory, setActiveCategory] = React.useState(CATEGORIES[0]);

    const stepsByCategory = (category) => stepConfiguration.filter((c) => c.category === category);

    return html`
        <div>
            <ul className="nav nav-tabs">
                ${CATEGORIES.map((category) => html`
                    <li key=${category} className="nav-item">
                        <a
                            href="#"
                            className="nav-link${activeCategory === category ? ' active' : ''}"
                            onClick=${(e) => { e.preventDefault(); setActiveCategory(category); }}
                        >
                            ${capitalize(category)}
                        </a>
                    </li>
                `)}
            </ul>

            ${CATEGORIES.map((category) => html`
                <div
                    key=${category}
                    className="border border-top-0 rounded-bottom${activeCategory === category ? '' : ' d-none'}"
                >
                    <div className="list-group list-group-flush">
                        ${stepsByCategory(category).map((config) => html`
                            <${DragItem}
                                key=${config.code}
                                config=${config}
                                onDragStart=${onDragStart}
                                onDragEnd=${onDragEnd}
                            />
                        `)}
                    </div>
                </div>
            `)}
        </div>
    `;
}
