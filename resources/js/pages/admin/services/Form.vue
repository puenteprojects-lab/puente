<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ServiceController from '@/actions/App/Http/Controllers/Admin/ServiceController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/admin/services';

interface Translation {
    title?: string;
    text?: string;
}

interface ServicePayload {
    id: number;
    slug: string;
    position: number;
    is_published: boolean;
    translations: Record<string, Translation>;
}

const props = defineProps<{
    service: ServicePayload | null;
    nextPosition: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Services', href: index() }],
    },
});

const page = usePage();
const i18n = computed(
    () =>
        page.props.i18n as {
            base: string;
            locales: { code: string; native: string }[];
        },
);

const isEdit = computed(() => props.service !== null);
const active = ref(i18n.value.base);
const published = ref(props.service?.is_published ?? true);

function value(locale: string, field: keyof Translation): string {
    return props.service?.translations?.[locale]?.[field] ?? '';
}

/** A locale still needs attention when the base one is filled and it is not. */
function isEmpty(locale: string): boolean {
    return !value(locale, 'title');
}
</script>

<template>
    <Head :title="isEdit ? 'Edit service' : 'New service'" />

    <div class="flex max-w-3xl flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="isEdit ? 'Edit service' : 'New service'"
            description="One card in the “What I work with” section. The base language is required; the others can be finished later."
        />

        <Form
            v-bind="
                isEdit
                    ? ServiceController.update.form(service!.id)
                    : ServiceController.store.form()
            "
            class="space-y-8"
            v-slot="{ errors, processing }"
        >
            <input
                type="hidden"
                name="is_published"
                :value="published ? 1 : 0"
            />

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="slug">Identifier</Label>
                    <Input
                        id="slug"
                        name="slug"
                        :default-value="service?.slug"
                        required
                        placeholder="anxiety-and-stress"
                    />
                    <p class="text-xs text-muted-foreground">
                        Lowercase letters, numbers and hyphens. Stays the same
                        across languages.
                    </p>
                    <InputError :message="errors.slug" />
                </div>

                <div class="grid gap-2">
                    <Label for="position">Position</Label>
                    <Input
                        id="position"
                        name="position"
                        type="number"
                        min="0"
                        :default-value="service?.position ?? nextPosition"
                        required
                    />
                    <p class="text-xs text-muted-foreground">
                        Lower numbers come first. Order can also be changed from
                        the list.
                    </p>
                    <InputError :message="errors.position" />
                </div>
            </div>

            <div class="flex items-center gap-3">
                <Checkbox id="published" v-model="published" />
                <Label for="published" class="font-normal"
                    >Show on the landing page</Label
                >
            </div>

            <div class="space-y-4">
                <div class="flex flex-wrap gap-2 border-b pb-3">
                    <button
                        v-for="locale in i18n.locales"
                        :key="locale.code"
                        type="button"
                        :class="[
                            'rounded-full px-3.5 py-1.5 text-sm transition-colors',
                            active === locale.code
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                        @click="active = locale.code"
                    >
                        {{ locale.native }}
                        <span
                            v-if="locale.code === i18n.base"
                            class="opacity-70"
                            >*</span
                        >
                        <span
                            v-else-if="isEdit && isEmpty(locale.code)"
                            class="opacity-70"
                            >?</span
                        >
                    </button>
                </div>

                <!--
                    Every language stays mounted so its values are submitted
                    even while another tab is on screen.
                -->
                <div
                    v-for="locale in i18n.locales"
                    :key="locale.code"
                    v-show="active === locale.code"
                    class="grid gap-5"
                >
                    <div class="grid gap-2">
                        <Label :for="`title-${locale.code}`">Title</Label>
                        <Input
                            :id="`title-${locale.code}`"
                            :name="`translations[${locale.code}][title]`"
                            :default-value="value(locale.code, 'title')"
                            :required="locale.code === i18n.base"
                        />
                        <InputError
                            :message="
                                (errors as Record<string, string>)[
                                    `translations.${locale.code}.title`
                                ]
                            "
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`text-${locale.code}`">Description</Label>
                        <textarea
                            :id="`text-${locale.code}`"
                            :name="`translations[${locale.code}][text]`"
                            rows="4"
                            :required="locale.code === i18n.base"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm"
                            :value="value(locale.code, 'text')"
                        ></textarea>
                        <InputError
                            :message="
                                (errors as Record<string, string>)[
                                    `translations.${locale.code}.text`
                                ]
                            "
                        />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <Button :disabled="processing">{{
                    isEdit ? 'Save changes' : 'Create service'
                }}</Button>
                <Button variant="ghost" as-child>
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
