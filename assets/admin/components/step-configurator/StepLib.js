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
            className="item drag-item${dragging ? ' dragging' : ''}"
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
            <div className="content">
                <div className="header">${config.name}</div>
                <div className="italic text">${config.code}</div>
                <div className="description">${config.description}</div>
            </div>
        </div>
    `;
}

export default function StepLib({ stepConfiguration, onDragStart, onDragEnd }) {
    const [activeCategory, setActiveCategory] = React.useState(CATEGORIES[0]);

    const stepsByCategory = (category) => stepConfiguration.filter((c) => c.category === category);

    return html`
        <div>
            <div className="ui top attached tabular menu">
                ${CATEGORIES.map((category) => html`
                    <a
                        key=${category}
                        className="item${activeCategory === category ? ' active' : ''}"
                        onClick=${() => setActiveCategory(category)}
                    >
                        ${capitalize(category)}
                    </a>
                `)}
            </div>

            ${CATEGORIES.map((category) => html`
                <div
                    key=${category}
                    className="ui bottom attached tab segment${activeCategory === category ? ' active' : ''}"
                >
                    <div className="ui divided selection list">
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
