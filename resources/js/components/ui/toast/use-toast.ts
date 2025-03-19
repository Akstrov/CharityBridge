import { ref } from 'vue'

interface ToastOptions {
    title?: string
    description?: string
    duration?: number
}

const toasts = ref<ToastOptions[]>([])

export function useToast() {
    const toast = (options: ToastOptions) => {
        toasts.value.push(options)
        if (options.duration) {
            setTimeout(() => {
                toasts.value = toasts.value.filter(t => t !== options)
            }, options.duration)
        }
    }

    return {
        toast,
        toasts
    }
} 