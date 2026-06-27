import type { Directive } from 'vue';

/**
 * Directiva global `v-click-outside`.
 * Ejecuta el callback cuando se hace clic fuera del elemento.
 *
 * Registro (en tu app.ts, donde creas la app Inertia):
 *   import { clickOutside } from '@/directives/clickOutside';
 *   app.directive('click-outside', clickOutside);
 *
 * Uso:
 *   <div v-click-outside="() => (open = false)"> ... </div>
 */
interface ClickOutsideEl extends HTMLElement {
    __clickOutsideHandler__?: (event: MouseEvent) => void;
}

export const clickOutside: Directive<ClickOutsideEl, () => void> = {
    mounted(el, binding) {
        el.__clickOutsideHandler__ = (event: MouseEvent) => {
            if (!(el === event.target || el.contains(event.target as Node))) {
                binding.value();
            }
        };
        // setTimeout evita que el mismo clic que abre el menú lo cierre.
        setTimeout(() => {
            document.addEventListener('click', el.__clickOutsideHandler__!);
        }, 0);
    },
    unmounted(el) {
        if (el.__clickOutsideHandler__) {
            document.removeEventListener('click', el.__clickOutsideHandler__);
        }
    },
};
