import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export interface LocaleLink {
    code: string;
    native: string;
    url: string;
    absolute: string;
    current: boolean;
}

interface I18nProps {
    locale: string;
    base: string;
    locales: LocaleLink[];
    messages: Record<string, unknown>;
}

/**
 * Reads the translations Laravel shares on every Inertia response.
 *
 * Keys use dot notation and mirror the structure of lang/{locale}/landing.php,
 * so `t('hero.title')` and `list('services.items')` both resolve against the
 * same file.
 */
export function useTranslations() {
    const page = usePage();

    const i18n = computed(() => page.props.i18n as I18nProps);

    function resolve(key: string): unknown {
        return key
            .split('.')
            .reduce<unknown>(
                (carry, segment) =>
                    carry && typeof carry === 'object'
                        ? (carry as Record<string, unknown>)[segment]
                        : undefined,
                i18n.value.messages,
            );
    }

    /** A single string. Falls back to the key so a gap is visible, not silent. */
    function t(key: string): string {
        const value = resolve(key);

        return typeof value === 'string' ? value : key;
    }

    /** A repeated block, such as the service cards or the FAQ entries. */
    function list<T = Record<string, string>>(key: string): T[] {
        const value = resolve(key);

        return Array.isArray(value) ? (value as T[]) : [];
    }

    return {
        t,
        list,
        locale: computed(() => i18n.value.locale),
        locales: computed(() => i18n.value.locales),
    };
}
