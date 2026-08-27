<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import PuenteMark from '@/components/PuenteMark.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { create, destroy, edit, index, reorder } from '@/routes/admin/services';

interface ServiceRow {
    id: number;
    slug: string;
    title: string;
    position: number;
    is_published: boolean;
    missing_locales: string[];
}

const props = defineProps<{ services: ServiceRow[] }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Services', href: index() }],
    },
});

const page = usePage();

watch(
    () => (page.props.flash as { status?: string } | undefined)?.status,
    (status) => status && toast.success(status),
    { immediate: true },
);

const ordered = computed(() => props.services);

/** Moves one service and saves the whole running order in one request. */
function move(index_: number, delta: number) {
    const ids = ordered.value.map((service) => service.id);
    const target = index_ + delta;

    if (target < 0 || target >= ids.length) {
        return;
    }

    [ids[index_], ids[target]] = [ids[target], ids[index_]];

    router.post(reorder().url, { ids }, { preserveScroll: true });
}

function remove(service: ServiceRow) {
    if (!confirm(`Delete “${service.title}”? This cannot be undone.`)) {
        return;
    }

    router.delete(destroy(service.id).url, { preserveScroll: true });
}
</script>

<template>
    <Head title="Services" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <Heading
                variant="small"
                title="Services"
                description="The cards shown in “What I work with” on the landing page."
            />
            <Button as-child>
                <Link :href="create()">Add service</Link>
            </Button>
        </div>

        <div
            v-if="ordered.length === 0"
            class="rounded-xl border border-dashed p-12 text-center"
        >
            <PuenteMark
                compact
                class="mx-auto h-8 w-auto text-muted-foreground"
            />
            <p class="mt-4 font-medium">No services yet</p>
            <p class="mt-1 text-sm text-muted-foreground">
                Until one is published the landing page falls back to the
                built-in wording.
            </p>
            <Button class="mt-6" as-child>
                <Link :href="create()">Add the first one</Link>
            </Button>
        </div>

        <ul v-else class="flex flex-col gap-3">
            <li
                v-for="(service, i) in ordered"
                :key="service.id"
                class="flex flex-wrap items-center gap-4 rounded-xl border p-4"
            >
                <div class="flex flex-col gap-1">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-6 w-6"
                        :disabled="i === 0"
                        aria-label="Move up"
                        @click="move(i, -1)"
                        >↑</Button
                    >
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-6 w-6"
                        :disabled="i === ordered.length - 1"
                        aria-label="Move down"
                        @click="move(i, 1)"
                        >↓</Button
                    >
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-medium">{{ service.title }}</span>
                        <Badge v-if="!service.is_published" variant="secondary"
                            >Hidden</Badge
                        >
                        <Badge
                            v-for="locale in service.missing_locales"
                            :key="locale"
                            variant="outline"
                            class="uppercase"
                            :title="`No wording for ${locale} yet`"
                            >{{ locale }} ?</Badge
                        >
                    </div>
                    <p class="mt-0.5 truncate text-sm text-muted-foreground">
                        {{ service.slug }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="edit(service.id)">Edit</Link>
                    </Button>
                    <Button
                        variant="ghost"
                        size="sm"
                        class="text-destructive"
                        @click="remove(service)"
                        >Delete</Button
                    >
                </div>
            </li>
        </ul>
    </div>
</template>
