import store from './store.js';

/**
 * StepLib — colonne gauche du configurateur.
 *
 * Responsabilités :
 *   - Afficher les steps disponibles groupés par catégorie (tabs Semantic UI)
 *   - Rendre chaque item draggable (dragstart / dragend)
 *   - Émettre un CustomEvent « step-lib:dragstart » et « step-lib:dragend »
 *     afin que step-configuration puisse réagir (afficher / masquer les drop-zones)
 *
 * Attributs observés :
 *   - (aucun) — les données viennent du store partagé
 */
export default class StepLib extends HTMLElement {

    #categories = ['extractor', 'transformer', 'loader'];
    #listeners  = [];

    connectedCallback() {
        this.render();
        this.#bindEvents();
    }

    disconnectedCallback() {
        this.#unbindEvents();
    }

    // ─── Rendu ──────────────────────────────────────────────────────────────

    render() {
        this.innerHTML = `
            <div class="ui top attached tabular menu">
                ${this.#categories.map((category, index) => `
                    <a
                        class="item ${index === 0 ? 'active' : ''}"
                        data-tab="step-lib-${category}"
                    >
                        ${this.#capitalize(category)}
                    </a>
                `).join('')}
            </div>

            ${this.#categories.map((category, index) => `
                <div
                    class="ui bottom attached tab segment ${index === 0 ? 'active' : ''}"
                    data-tab="step-lib-${category}"
                >
                    <div class="ui divided selection list">
                        ${this.#getStepsByCategory(category).map(config => `
                            <div
                                class="item drag-item"
                                data-code="${config.code}"
                                draggable="true"
                            >
                                <div class="content">
                                    <div class="header">${config.name}</div>
                                    <div class="italic text">${config.code}</div>
                                    <div class="description">${config.description}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('')}
        `;

        // Activation des onglets Semantic UI (si jQuery / $.fn.tab est disponible)
        if (typeof $ !== 'undefined' && $.fn.tab) {
            $(this).find('.menu .item').tab();
        }
    }

    // ─── Événements ─────────────────────────────────────────────────────────

    #bindEvents() {
        this.#unbindEvents();

        this.querySelectorAll('.drag-item').forEach(item => {
            const onDragStart = (e) => {
                e.dataTransfer.setData('text/plain', item.dataset.code);
                item.classList.add('dragging');
                this.dispatchEvent(new CustomEvent('step-lib:dragstart', {
                    bubbles: true,
                    detail: { code: item.dataset.code },
                }));
            };

            const onDragEnd = () => {
                item.classList.remove('dragging');
                this.dispatchEvent(new CustomEvent('step-lib:dragend', {
                    bubbles: true,
                }));
            };

            item.addEventListener('dragstart', onDragStart);
            item.addEventListener('dragend',   onDragEnd);

            this.#listeners.push(
                { element: item, type: 'dragstart', listener: onDragStart },
                { element: item, type: 'dragend',   listener: onDragEnd   },
            );
        });
    }

    #unbindEvents() {
        this.#listeners.forEach(({ element, type, listener }) =>
            element.removeEventListener(type, listener)
        );
        this.#listeners = [];
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    #getStepsByCategory(category) {
        return store.stepConfiguration.filter(c => c.category === category);
    }

    #capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
}

customElements.define('step-lib', StepLib);
