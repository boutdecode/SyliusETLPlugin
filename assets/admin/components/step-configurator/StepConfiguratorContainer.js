import React from 'react';
import html from '../../html.js';
import Icon from '../../icons.js';
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

export default function StepConfiguratorContainer({ initialSteps, stepConfiguration, onChange, labels }) {
    const [steps, setSteps] = React.useState(initialSteps);
    const [isDragging, setIsDragging] = React.useState(false);
    const [isFullscreen, setIsFullscreen] = React.useState(false);

    React.useEffect(() => {
        if (!isFullscreen) return undefined;

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        const onKeyDown = (event) => {
            if (event.key === 'Escape') setIsFullscreen(false);
        };
        document.addEventListener('keydown', onKeyDown);

        return () => {
            document.body.style.overflow = previousOverflow;
            document.removeEventListener('keydown', onKeyDown);
        };
    }, [isFullscreen]);
    const isFirstRender = React.useRef(true);
    // Stable per-step keys: step inputs are uncontrolled, so cards must not be matched by position.
    const nextStepId = React.useRef(initialSteps.length);
    const stepIds = React.useRef(initialSteps.map((_, i) => i));

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
        stepIds.current.splice(order, 0, nextStepId.current++);
        commit(nextSteps);
    };

    const handleMoveUp = (index) => {
        if (index <= 0) return;
        const nextSteps = [...steps];
        [nextSteps[index - 1], nextSteps[index]] = [nextSteps[index], nextSteps[index - 1]];
        [stepIds.current[index - 1], stepIds.current[index]] = [stepIds.current[index], stepIds.current[index - 1]];
        commit(nextSteps);
    };

    const handleMoveDown = (index) => {
        if (index >= steps.length - 1) return;
        const nextSteps = [...steps];
        [nextSteps[index + 1], nextSteps[index]] = [nextSteps[index], nextSteps[index + 1]];
        [stepIds.current[index + 1], stepIds.current[index]] = [stepIds.current[index], stepIds.current[index + 1]];
        commit(nextSteps);
    };

    const handleRemove = (index) => {
        const nextSteps = [...steps];
        nextSteps.splice(index, 1);
        stepIds.current.splice(index, 1);
        commit(nextSteps);
    };

    const handleConfigChange = (index, key, value) => {
        const nextSteps = [...steps];
        const step = nextSteps[index];
        const configuration = Array.isArray(step.configuration) ? {} : { ...step.configuration };

        if (value === undefined) {
            delete configuration[key];
        } else {
            configuration[key] = value;
        }

        nextSteps[index] = { ...step, configuration };
        commit(nextSteps);
    };

    const fullscreenLabel = labels?.fullscreen || 'Fullscreen';
    const exitFullscreenLabel = labels?.exitFullscreen || 'Exit fullscreen';

    return html`
        <div className=${'configurator-wrapper' + (isFullscreen ? ' is-fullscreen' : '')}>
            <div className="configurator-toolbar">
                <button type="button" className="btn btn-sm" onClick=${() => setIsFullscreen((v) => !v)}>
                    <${Icon} name=${isFullscreen ? 'compress' : 'expand'} />
                    ${isFullscreen ? exitFullscreenLabel : fullscreenLabel}
                </button>
            </div>
            <section className="configurator-container row g-3">
                <div className="configurator-step-config-container col-12 col-lg-4" style=${{ overflowY: 'auto' }}>
                    <${StepLib}
                        stepConfiguration=${stepConfiguration}
                        onDragStart=${() => setIsDragging(true)}
                        onDragEnd=${() => setIsDragging(false)}
                    />
                </div>
                <div className="configurator-steps-container col-12 col-lg-8" style=${{ overflowY: 'auto' }}>
                    <${StepConfiguration}
                        steps=${steps}
                        stepIds=${stepIds.current}
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
        </div>
    `;
}
