const INPUT_TYPES = ['text', 'file', 'json'];

const LABELS = {
    text: 'Texte',
    file: 'Fichier',
    json: 'JSON',
};

export default class PipelineInputSelector extends HTMLElement {
    #listeners = [];
    #currentType = 'json';
    #textarea = null;
    #fileWrapper = null;

    connectedCallback() {
        this.#currentType = this.dataset.initialType ?? 'json';
        this.#textarea = document.getElementById(this.dataset.textareaId);
        this.#fileWrapper = document.getElementById(this.dataset.fileFieldId);

        this.#render();
        this.#applyVisibility();
        this.#bindEvents();
    }

    disconnectedCallback() {
        this.#unbindEvents();
    }

    #render() {
        this.innerHTML = `
            <div class="ui secondary pointing menu pipeline-input-tabs">
                ${INPUT_TYPES.map(type => `
                    <a class="item${this.#currentType === type ? ' active' : ''}" data-type="${type}">
                        ${LABELS[type]}
                    </a>
                `).join('')}
            </div>
        `;
    }

    #applyVisibility() {
        if (!this.#textarea || !this.#fileWrapper) return;

        const isFile = this.#currentType === 'file';
        const textareaRow = this.#textarea.closest('.field') ?? this.#textarea.parentElement;
        if (textareaRow) textareaRow.style.display = isFile ? 'none' : '';

        this.#fileWrapper.style.display = isFile ? '' : 'none';
    }

    #bindEvents() {
        this.#unbindEvents();

        this.querySelectorAll('.pipeline-input-tabs .item').forEach(tab => {
            const onClick = () => this.#switchTab(tab.dataset.type);
            tab.addEventListener('click', onClick);
            this.#listeners.push({ element: tab, type: 'click', listener: onClick });
        });
    }

    #unbindEvents() {
        this.#listeners.forEach(({ element, type, listener }) => element.removeEventListener(type, listener));
        this.#listeners = [];
    }

    #switchTab(type) {
        this.#currentType = type;

        this.querySelectorAll('.pipeline-input-tabs .item').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.type === type);
        });

        this.#applyVisibility();
    }
}

customElements.define('pipeline-input-selector', PipelineInputSelector);
