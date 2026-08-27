<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PuenteMark from '@/components/PuenteMark.vue';
import { useTranslations } from '@/composables/useTranslations';
import { dashboard, login } from '@/routes';

const props = defineProps<{ services?: Service[] }>();

const { t, list, locales } = useTranslations();

// Услуги приходят из базы; пока там пусто — показываем встроенные тексты,
// чтобы секция никогда не оказалась пустой.
const services = computed(() =>
    props.services?.length ? props.services : list<Service>('services.items'),
);

const nav = [
    { key: 'nav.services', href: '#services' },
    { key: 'nav.process', href: '#process' },
    { key: 'nav.about', href: '#about' },
    { key: 'nav.pricing', href: '#pricing' },
    { key: 'nav.faq', href: '#faq' },
];

interface Service {
    title: string;
    text: string;
}
interface Step {
    n: string;
    title: string;
    text: string;
}
interface Plan {
    title: string;
    duration: string;
    price: string;
    note: string;
}
interface Question {
    q: string;
    a: string;
}
</script>

<template>
    <Head :title="`Puente — ${t('footer.tagline')}`">
        <meta name="description" :content="t('hero.lead')" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin=""
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..600&display=swap"
            rel="stylesheet"
        />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-brand-sand font-sans text-brand-teal">
        <!-- Шапка -->
        <header
            class="sticky top-0 z-50 border-b border-brand-teal/10 bg-brand-sand/85 backdrop-blur-md"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-6 py-4"
            >
                <a href="#top" class="flex shrink-0 items-center gap-3">
                    <PuenteMark compact class="h-6 w-auto text-brand-teal" />
                    <span
                        class="font-display text-xl font-semibold tracking-tight"
                        >Puente</span
                    >
                </a>

                <nav class="hidden items-center gap-7 text-sm lg:flex">
                    <a
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="text-brand-teal/75 transition-colors hover:text-brand-teal"
                        >{{ t(item.key) }}</a
                    >
                </nav>

                <div class="flex items-center gap-3">
                    <!-- Переключатель языка -->
                    <div class="hidden items-center gap-1 text-sm md:flex">
                        <a
                            v-for="option in locales"
                            :key="option.code"
                            :href="option.url"
                            :hreflang="option.code"
                            :aria-current="option.current ? 'true' : undefined"
                            :class="[
                                'rounded-full px-2.5 py-1 uppercase transition-colors',
                                option.current
                                    ? 'bg-brand-teal text-white'
                                    : 'text-brand-teal/60 hover:text-brand-teal',
                            ]"
                            >{{ option.code }}</a
                        >
                    </div>

                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="hidden text-sm text-brand-teal/70 hover:text-brand-teal sm:inline"
                        >{{ t('nav.account') }}</Link
                    >
                    <Link
                        v-else
                        :href="login()"
                        class="hidden text-sm text-brand-teal/70 hover:text-brand-teal sm:inline"
                        >{{ t('nav.login') }}</Link
                    >
                    <a
                        href="#contact"
                        class="shrink-0 rounded-full bg-brand-teal px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-brand-teal-dark"
                        >{{ t('nav.book') }}</a
                    >
                </div>
            </div>
        </header>

        <!-- Первый экран -->
        <section id="top">
            <div
                class="mx-auto grid max-w-6xl items-center gap-14 px-6 py-20 lg:grid-cols-[1.15fr_1fr] lg:py-28"
            >
                <div>
                    <p
                        class="mb-5 inline-flex items-center gap-2 rounded-full border border-brand-teal/15 bg-white/70 px-4 py-1.5 text-sm text-brand-terracotta-deep"
                    >
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-brand-terracotta"
                        />
                        {{ t('hero.badge') }}
                    </p>

                    <h1
                        class="font-display text-4xl leading-[1.1] font-semibold tracking-tight text-balance sm:text-5xl lg:text-6xl"
                    >
                        {{ t('hero.title') }}
                    </h1>

                    <p
                        class="mt-6 max-w-xl text-lg leading-relaxed text-brand-teal/80"
                    >
                        {{ t('hero.lead') }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-4">
                        <a
                            href="#contact"
                            class="rounded-full bg-brand-teal px-7 py-3.5 font-medium text-white transition-colors hover:bg-brand-teal-dark"
                            >{{ t('hero.cta_primary') }}</a
                        >
                        <a
                            href="#process"
                            class="rounded-full border border-brand-teal/25 px-7 py-3.5 font-medium transition-colors hover:border-brand-teal/50"
                            >{{ t('hero.cta_secondary') }}</a
                        >
                    </div>

                    <p class="mt-5 text-sm text-brand-teal/60">
                        {{ t('hero.note') }}
                    </p>
                </div>

                <div
                    class="relative aspect-square overflow-hidden rounded-[2rem] bg-brand-teal"
                >
                    <!-- TODO: заменить на фотографию специалиста -->
                    <PuenteMark
                        compact
                        class="absolute top-1/2 left-1/2 w-1/2 -translate-x-1/2 -translate-y-[60%] text-white/95"
                    />
                    <p
                        class="absolute inset-x-0 bottom-0 p-8 text-center text-sm text-white/60"
                    >
                        {{ t('hero.photo') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- С чем работаю -->
        <section id="services" class="bg-white py-20 lg:py-28">
            <div class="mx-auto max-w-6xl px-6">
                <h2
                    class="font-display text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('services.title') }}
                </h2>
                <p class="mt-4 max-w-2xl text-lg text-brand-teal/75">
                    {{ t('services.lead') }}
                </p>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="service in services"
                        :key="service.title"
                        class="rounded-2xl border border-brand-teal/10 bg-brand-sand p-7 transition-colors hover:border-brand-teal/25"
                    >
                        <PuenteMark
                            compact
                            class="h-6 w-auto text-brand-teal/70"
                        />
                        <h3 class="mt-5 font-display text-xl font-semibold">
                            {{ service.title }}
                        </h3>
                        <p class="mt-2.5 leading-relaxed text-brand-teal/75">
                            {{ service.text }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Как проходит -->
        <section id="process" class="py-20 lg:py-28">
            <div class="mx-auto max-w-6xl px-6">
                <h2
                    class="font-display text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('process.title') }}
                </h2>

                <div class="mt-12 grid gap-10 md:grid-cols-3">
                    <div
                        v-for="step in list<Step>('process.steps')"
                        :key="step.n"
                    >
                        <span
                            class="font-display text-5xl font-semibold text-brand-terracotta"
                            >{{ step.n }}</span
                        >
                        <h3 class="mt-4 font-display text-xl font-semibold">
                            {{ step.title }}
                        </h3>
                        <p class="mt-2.5 leading-relaxed text-brand-teal/75">
                            {{ step.text }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Обо мне -->
        <section id="about" class="bg-brand-teal py-20 text-white lg:py-28">
            <div
                class="mx-auto grid max-w-6xl items-center gap-14 px-6 lg:grid-cols-[1fr_1.2fr]"
            >
                <!-- TODO: заменить на портрет -->
                <div
                    class="aspect-[4/5] rounded-[2rem] border border-white/15 bg-white/5 p-12"
                >
                    <PuenteMark compact class="mx-auto w-3/5 text-white/90" />
                </div>

                <div>
                    <h2
                        class="font-display text-3xl font-semibold tracking-tight sm:text-4xl"
                    >
                        {{ t('about.title') }}
                    </h2>
                    <div
                        class="mt-6 space-y-4 text-lg leading-relaxed text-white/85"
                    >
                        <p>{{ t('about.p1') }}</p>
                        <!-- TODO: образование, метод, стаж, членство в ассоциациях -->
                        <p>{{ t('about.p2') }}</p>
                        <p>{{ t('about.p3') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Стоимость -->
        <section id="pricing" class="bg-white py-20 lg:py-28">
            <div class="mx-auto max-w-6xl px-6">
                <h2
                    class="font-display text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('pricing.title') }}
                </h2>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    <article
                        v-for="(plan, index) in list<Plan>('pricing.items')"
                        :key="plan.title"
                        :class="[
                            'rounded-2xl border p-8',
                            index === 1
                                ? 'border-brand-teal bg-brand-sand shadow-sm'
                                : 'border-brand-teal/12',
                        ]"
                    >
                        <h3 class="font-display text-xl font-semibold">
                            {{ plan.title }}
                        </h3>
                        <p class="mt-1 text-sm text-brand-teal/60">
                            {{ plan.duration }}
                        </p>
                        <p
                            class="mt-6 font-display text-3xl font-semibold text-brand-terracotta-deep"
                        >
                            {{ plan.price }}
                        </p>
                        <p class="mt-3 text-brand-teal/75">{{ plan.note }}</p>
                    </article>
                </div>

                <!-- TODO: проставить реальные цены и условия отмены -->
                <p class="mt-6 text-sm text-brand-teal/60">
                    {{ t('pricing.note') }}
                </p>
            </div>
        </section>

        <!-- Вопросы -->
        <section id="faq" class="py-20 lg:py-28">
            <div class="mx-auto max-w-3xl px-6">
                <h2
                    class="font-display text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('faq.title') }}
                </h2>

                <div class="mt-10 divide-y divide-brand-teal/12">
                    <details
                        v-for="item in list<Question>('faq.items')"
                        :key="item.q"
                        class="group py-5"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center justify-between gap-6 font-medium"
                        >
                            {{ item.q }}
                            <span
                                class="shrink-0 text-2xl leading-none text-brand-terracotta transition-transform group-open:rotate-45"
                                >+</span
                            >
                        </summary>
                        <p class="mt-3 leading-relaxed text-brand-teal/75">
                            {{ item.a }}
                        </p>
                    </details>
                </div>
            </div>
        </section>

        <!-- Призыв -->
        <section id="contact" class="bg-brand-teal py-20 text-white lg:py-24">
            <div class="mx-auto max-w-3xl px-6 text-center">
                <PuenteMark compact class="mx-auto h-11 w-auto text-white" />
                <h2
                    class="mt-7 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl"
                >
                    {{ t('contact.title') }}
                </h2>
                <p class="mt-5 text-lg text-white/80">
                    {{ t('contact.lead') }}
                </p>
                <!-- TODO: заменить на реальные контакты -->
                <a
                    href="mailto:hola@puente.es"
                    class="mt-9 inline-block rounded-full bg-white px-8 py-4 font-medium text-brand-teal transition-colors hover:bg-brand-sand"
                    >{{ t('contact.cta') }}</a
                >
                <p class="mt-5 text-sm text-white/60">
                    {{ t('contact.note') }}
                </p>
            </div>
        </section>

        <!-- Подвал -->
        <footer class="border-t border-brand-teal/10 py-10">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-5 px-6 text-sm text-brand-teal/60 sm:flex-row"
            >
                <div class="flex items-center gap-2.5">
                    <PuenteMark compact class="h-4 w-auto text-brand-teal/70" />
                    <span>Puente · {{ t('footer.tagline') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <a
                        v-for="option in locales"
                        :key="option.code"
                        :href="option.url"
                        :hreflang="option.code"
                        class="transition-colors hover:text-brand-teal"
                        >{{ option.native }}</a
                    >
                </div>

                <p>© {{ new Date().getFullYear() }}</p>
            </div>
        </footer>
    </div>
</template>
