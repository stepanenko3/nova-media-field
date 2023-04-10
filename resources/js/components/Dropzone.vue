<template>
    <div
        class="rounded-xl p-1.5 border-2 border-dashed border-primary-100 dark:border-gray-700 relative h-48 group"
    >
        <input
            :id="attribute"
            :multiple="multiple"
            :disabled="uploading"
            type="file"
            class="absolute w-full h-full opacity-0 cursor-pointer"
            accept="image/jpg,image/jpeg,image/png,jpg,png"
            @change="upload"
        />

        <div
            class="flex flex-col justify-center items-center space-y-2 w-full h-full rounded-lg dark:bg-gray-900 bg-primary-50 transition-all ease-in-out duration-300 dark:group-hover:bg-gray-900"
        >
            <div class="text-primary-500">
                <svg
                    class="fill-current w-8 h-8"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M7.1 12.7l2.9-2.6 3 2.6-3-2.6V16"
                    ></path>
                    <path
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.1 16c2.2 0 3.9-1.7 3.9-3.9s-1.7-3.9-3.9-3.9c-.8 0-1.6.2-2.3.7-.6-3.2-3.7-5.4-6.9-4.8s-5.4 3.7-4.8 7c.6 2.8 3.1 4.9 6 4.9h8z"
                    ></path>
                </svg>
            </div>

            <div class="font-semibold text-gray-900 dark:text-gray-200">
                <span class="hidden md:inline"
                    >Drag &amp; Drop your files here or
                </span>
                <span class="text-primary-500"> Browse Files </span>
            </div>

            <div
                class="flex flex-col space-y-1 text-xs font-semibold text-center sm:flex-row sm:space-y-0 sm:space-x-1 text-gray-500 chunk-header"
                v-if="helpText"
            >
                <span>{{ helpText }}</span>
            </div>
        </div>

        <div v-show="uploading">
            <div
                class="cursor-pointer flex items-center justify-center absolute top-0 opacity-90 transition-all ease-in-out duration-300 bg-gray-900 w-full h-full right-0 bottom-0 left-0 rounded-xl"
            >
                <svg
                    class="fill-current w-20 h-20 text-white animation-spin duration-1000"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    xml:space="preserve"
                >
                    <circle
                        fill="none"
                        stroke="var(--icon-color-secondary, #E5F0F8)"
                        stroke-width="2"
                        stroke-miterlimit="10"
                        cx="10"
                        cy="10"
                        r="9"
                    ></circle>

                    <path
                        fill="none"
                        stroke="rgb(var(--colors-primary-500))"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-miterlimit="10"
                        d="M18.7 7.8c-.7-3-3-5.4-5.8-6.4"
                    ></path>
                </svg>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    helpText: {
        type: String,
    },
    attribute: {
        type: String,
        required: true,
    },
    uploading: {
        type: Boolean,
        default: false,
    },
});

function upload(e) {
    console.log(e);
}
</script>
