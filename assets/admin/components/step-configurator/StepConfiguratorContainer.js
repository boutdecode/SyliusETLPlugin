import React from 'react';
import html from '../../html.js';
import StepLib from './StepLib.js';
import StepConfiguration from './StepConfiguration.js';

function applyDuplicateNames(steps) {
    const counts = {};

    return steps.map((step) => {
        counts[step.code] = (counts[step.code] ?? 0) + 1;

        if (counts[step.code] > 1) {
            return { ...step, name: `${step.code}-${counts[step.code]}` };
        }

        const { name, ...rest } = step;
        return rest;
    });
}

export default function StepConfiguratorContainer({ initialSteps, stepConfiguration, onChange }) {
    const [steps, setSteps] = React.useState(initialSteps);
    const [isDragging, setIsDragging] = React.useState(false);
    const isFirstRender = React.useRef(true);

    React.useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }
        onChange(steps);
    }, [steps]);

    const commit = (nextSteps) => setSteps(applyDuplicateNames(nextSteps));

    const handleDrop = (order, code) => {
        const stepConfig = stepConfiguration.find((c) => c.code === code);
        if (!stepConfig) return;

        const nextSteps = [...steps];
        nextSteps.splice(order, 0, { code: stepConfig.code, configuration: {} });
        commit(nextSteps);
    };

    const handleMoveUp = (index) => {
        if (index <= 0) return;
        const nextSteps = [...steps];
        [nextSteps[index - 1], nextSteps[index]] = [nextSteps[index], nextSteps[index - 1]];
        commit(nextSteps);
    };

    const handleMoveDown = (index) => {
        if (index >= steps.length - 1) return;
        const nextSteps = [...steps];
        [nextSteps[index + 1], nextSteps[index]] = [nextSteps[index], nextSteps[index + 1]];
        commit(nextSteps);
    };

    const handleRemove = (index) => {
        const nextSteps = [...steps];
        nextSteps.splice(index, 1);
        commit(nextSteps);
    };

    const handleConfigChange = (index, key, value) => {
        const nextSteps = [...steps];
        const step = nextSteps[index];
        const configuration = Array.isArray(step.configuration) ? {} : { ...step.configuration };

        if (value === '') {
            delete configuration[key];
        } else {
            configuration[key] = value;
        }

        nextSteps[index] = { ...step, configuration };
        commit(nextSteps);
    };

    return html`
        <section className="configurator-container ui grid">
            <div className="configurator-step-config-container five wide column" style=${{ overflowY: 'auto' }}>
                <${StepLib}
                    stepConfiguration=${stepConfiguration}
                    onDragStart=${() => setIsDragging(true)}
                    onDragEnd=${() => setIsDragging(false)}
                />
            </div>
            <div className="configurator-steps-container eleven wide column" style=${{ overflowY: 'auto' }}>
                <${StepConfiguration}
                    steps=${steps}
                    stepConfiguration=${stepConfiguration}
                    isDragging=${isDragging}
                    onDrop=${handleDrop}
                    onMoveUp=${handleMoveUp}
                    onMoveDown=${handleMoveDown}
                    onRemove=${handleRemove}
                    onConfigChange=${handleConfigChange}
                />
            </div>
        </section>
    `;
}
