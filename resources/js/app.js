import './bootstrap';
import './darkmode';
import '../../vendor/mijnui/mijnui/dist/mijnui.js';
import Sortable from 'sortablejs';
import * as d3 from 'd3';

window.d3 = d3;

if (!localStorage.getItem('mijnuiActiveContent')) {
    localStorage.setItem('mijnuiActiveContent', 'dashboard');
}

document.addEventListener("alpine:init", () => {
    Alpine.data("sortableList", () => ({
        init() {
            let el = this.$refs.phaseList;
            Sortable.create(el, {
                animation: 150,
                handle: ".drag-handle",
                onEnd: (evt) => {
                    let order = Array.from(el.children).map(i => i.getAttribute("data-id"));
                    this.$wire.updatePhaseOrder(order);
                }
            });
        }
    }));
});

