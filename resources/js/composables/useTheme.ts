import { onMounted, ref } from 'vue';

/**
 * Composable de tema claro/oscuro para CINEJULIOS.
 * Persiste la preferencia en localStorage y aplica la clase `dark`
 * al <html>. Sincronizado con la clave 'appearance' del starter kit.
 */
type Theme = 'light' | 'dark';

const theme = ref<Theme>('dark');

export function useTheme() {
    const applyTheme = (value: Theme) => {
        theme.value = value;
        const root = document.documentElement;
        if (value === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        localStorage.setItem('appearance', value);
        if (typeof document !== 'undefined') {
            document.cookie = `appearance=${value};path=/;max-age=31536000;SameSite=Lax`;
        }
    };

    const toggleTheme = () => {
        applyTheme(theme.value === 'dark' ? 'light' : 'dark');
    };

    onMounted(() => {
        const stored = (localStorage.getItem('appearance') as Theme) || 'dark';
        applyTheme(stored);
    });

    return { theme, toggleTheme, applyTheme };
}
