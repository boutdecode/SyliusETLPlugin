import html from './html.js';

// Tabler icon paths (24x24, stroke): the admin icons are rendered server-side, so the widgets embed the few they need.
const PATHS = {
    plus: ['M12 5l0 14', 'M5 12l14 0'],
    trash: ['M4 7l16 0', 'M10 11l0 6', 'M14 11l0 6', 'M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12', 'M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3'],
    up: ['M6 15l6 -6l6 6'],
    down: ['M6 9l6 6l6 -6'],
    chevron: ['M9 6l6 6l-6 6'],
    expand: ['M4 8v-2a2 2 0 0 1 2 -2h2', 'M4 16v2a2 2 0 0 0 2 2h2', 'M16 4h2a2 2 0 0 1 2 2v2', 'M16 20h2a2 2 0 0 0 2 -2v-2'],
    compress: ['M15 19v-2a2 2 0 0 1 2 -2h2', 'M15 5v2a2 2 0 0 0 2 2h2', 'M5 15h2a2 2 0 0 1 2 2v2', 'M5 9h2a2 2 0 0 0 2 -2v-2'],
    help: ['M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0', 'M12 17l0 .01', 'M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4'],
};

export default function Icon({ name, className = '' }) {
    return html`
        <svg
            xmlns="http://www.w3.org/2000/svg"
            className=${'icon ' + className}
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            ${PATHS[name].map((d) => html`<path key=${d} d=${d} />`)}
        </svg>
    `;
}
