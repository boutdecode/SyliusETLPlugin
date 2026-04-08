import './pipeline-input/pipeline-input-selector.js';

export default class PipelineInputController {
    constructor(element) {
        this.element = element;
    }

    initialize() {
        const initialType = this.#detectType(this.element.value ?? '');
        const fileWrapper = document.getElementById(this.element.dataset.fileFieldId);
        const textareaRow = this.element.closest('.field') ?? this.element.parentElement;
        const selector = document.createElement('pipeline-input-selector');
        selector.dataset.initialType  = initialType;
        selector.dataset.textareaId   = this.element.id;
        selector.dataset.fileFieldId  = this.element.dataset.fileFieldId;
        textareaRow.before(selector);

        if (fileWrapper && initialType !== 'file') {
            fileWrapper.style.display = 'none';
        }
    }

    #detectType(value) {
        if (!value || value === 'null' || value === '[]' || value === '{}') {
            return 'json';
        }
        try {
            const parsed = JSON.parse(value);
            if (parsed && typeof parsed === 'object' && parsed._file_path) {
                return 'file';
            }
            return 'json';
        } catch {
            return 'text';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-controller="pipeline-input"]').forEach(element => {
        new PipelineInputController(element).initialize();
    });
});
