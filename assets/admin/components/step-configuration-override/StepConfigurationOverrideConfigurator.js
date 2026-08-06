import React from 'react';
import html from '../../html.js';
import StepConfigurationOverride from './StepConfigurationOverride.js';

export default function StepConfigurationOverrideConfigurator({ steps, stepConfiguration, onChange }) {
    const [overrideByIndex, setOverrideByIndex] = React.useState({});
    const isFirstRender = React.useRef(true);

    React.useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const override = Object.keys(overrideByIndex)
            .sort((a, b) => Number(a) - Number(b))
            .map((index) => overrideByIndex[index]);

        onChange(override);
    }, [overrideByIndex]);

    const handleFieldChange = (index, key, value) => {
        setOverrideByIndex((prev) => {
            const entry = prev[index] ?? {
                code: steps[index].code,
                name: steps[index].name,
                configuration: {},
            };

            const configuration = { ...entry.configuration };
            if (value === undefined) {
                delete configuration[key];
            } else {
                configuration[key] = value;
            }

            return {
                ...prev,
                [index]: {
                    ...entry,
                    configuration,
                },
            };
        });
    };

    return html`
        <section className="configuration-override-container ui grid">
            <div className="configuration-override-steps-container">
                <${StepConfigurationOverride}
                    steps=${steps}
                    stepConfiguration=${stepConfiguration}
                    onFieldChange=${handleFieldChange}
                />
            </div>
        </section>
    `;
}
