import html from './html.js';

// Icônes Tabler en SVG inline (le plugin ne dépend pas d'une police d'icônes).
const svg = (paths, size = 16) => html`
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width=${size}
        height=${size}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
    >
        ${paths.map((d) => html`<path key=${d} d=${d} />`)}
    </svg>
`;

export const PlusIcon = () => svg(['M12 5l0 14', 'M5 12l14 0']);
export const ChevronUpIcon = () => svg(['M6 15l6 -6l6 6']);
export const ChevronDownIcon = () => svg(['M6 9l6 6l6 -6']);
export const ChevronRightIcon = () => svg(['M9 6l6 6l-6 6']);
export const TrashIcon = () => svg([
    'M4 7l16 0',
    'M10 11l0 6',
    'M14 11l0 6',
    'M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12',
    'M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3',
]);
export const HelpIcon = () => svg([
    'M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0',
    'M12 17l0 .01',
    'M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4',
]);
