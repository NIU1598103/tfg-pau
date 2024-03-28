<script setup>
    import {Head, Link} from '@inertiajs/inertia-vue3';
    // import AppLayout from '@/Layouts/AppLayout.vue';
    import AppLayout from '@/Layouts/AppLayout.vue';
    defineProps({
        posts: Array,
    });
</script>

<template>
    <AppLayout>
        <Head title="Posts" />
        <h1>Posts</h1>
        <Link href="/posts/create">Create Post</Link>
    </AppLayout>
</template>