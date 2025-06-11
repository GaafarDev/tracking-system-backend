<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
});
</script>
<template>
    <div>
        <button v-if="as == 'button'" type="submit" class="dropdown-link w-full text-start">
            <slot />
        </button>

        <a v-else-if="as =='a'" :href="href" class="dropdown-link">
            <slot />
        </a>

        <Link v-else :href="href" class="dropdown-link">
            <slot />
        </Link>
    </div>
</template>
