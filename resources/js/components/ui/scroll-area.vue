<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps<{
    class?: string;
}>();

const scrollArea = ref<HTMLElement | null>(null);

onMounted(() => {
    if (scrollArea.value) {
        // Add custom scrollbar styles
        const style = document.createElement('style');
        style.textContent = `
            .scroll-area::-webkit-scrollbar {
                width: 8px;
            }
            .scroll-area::-webkit-scrollbar-track {
                background: transparent;
            }
            .scroll-area::-webkit-scrollbar-thumb {
                background: #e2e8f0;
                border-radius: 4px;
            }
            .scroll-area::-webkit-scrollbar-thumb:hover {
                background: #cbd5e1;
            }
        `;
        document.head.appendChild(style);
    }
});
</script>

<template>
    <div
        ref="scrollArea"
        class="scroll-area overflow-auto"
        :class="props.class"
    >
        <slot></slot>
    </div>
</template> 