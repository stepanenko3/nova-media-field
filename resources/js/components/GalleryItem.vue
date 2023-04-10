<template>
    <li class="relative">
        <div
            class="overflow-hidden"
            :class="{
                'border-transparent': !error,
                'border-red-500': !!error,
                'pointer-events-none opacity-50': value.processing,
                'cursor-pointer': !value.processing,
            }"
        >
            <div class="relative select-none group w-full h-40">
                <GalleryPicture :value="value" :field="field" />

                <div
                    v-if="!readonly && !value.processing && draggable"
                    class="absolute inset-0 transition-all ease-in-out duration-300 opacity-0 group-hover:opacity-100"
                >
                    <div
                        class="rounded-lg flex flex-col items-center justify-center overlay-content-bg w-full h-full cursor-pointer"
                    >
                        <svg
                            class="fill-current w-8 h-8 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 32 32"
                            xml:space="preserve"
                        >
                            <path
                                d="M20.3 12.4V11c0-.8-.6-1.4-1.4-1.4s-1.4.6-1.4 1.4v-.7c0-.8-.6-1.4-1.4-1.4-.8 0-1.4.6-1.4 1.4v.7c0-.8-.6-1.4-1.4-1.4-.8 0-1.4.6-1.4 1.4v2.2c-1.6 0-2.9 1.3-2.9 2.9 0 1.4.4 2.8 1.3 3.9l1.1 1.5c.8 1.1 2.1 1.8 3.5 1.8h3.7c1.3 0 2.6-.5 3.5-1.4s1.4-2.2 1.4-3.5v-5.9c0-.8-.6-1.4-1.4-1.4-1.1-.1-1.8.5-1.8 1.3h0zm-8.6.2v2.2m2.9-2.9v-1.4m2.9 1.4v-1.4m1.6-6.4L16 1l-3.1 3.1m6.2 23.8L16 31l-3.1-3.1m-8.8-15L1 16l3.1 3.1m23.8-6.2L31 16l-3.1 3.1"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            ></path>
                        </svg>

                        <p class="mt-3 text-xs font-semibold text-gray-500">
                            Drag to reposition
                        </p>
                    </div>
                </div>

                <div
                    v-if="value.processing"
                    v-cloak
                    class="flex items-center justify-center absolute inset-0 transition-default bg-theme-secondary-900 w-full h-full z-10"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        xml:space="preserve"
                        class="w-20 h-20 text-white animation-spin duration-1000"
                    >
                        <circle
                            fill="none"
                            stroke="var(--icon-color-secondary, #E5F0F8)"
                            stroke-width="2"
                            stroke-miterlimit="10"
                            cx="10"
                            cy="10"
                            r="9"
                        />
                        <path
                            fill="none"
                            stroke="rgb(var(--colors-primary-500))"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-miterlimit="10"
                            d="M18.7 7.8c-.7-3-3-5.4-5.8-6.4"
                        />
                    </svg>
                </div>
            </div>

            <div class="flex pt-2">
                <div class="pr-2 flex-grow">
                    <b
                        class="line-clamp-1"
                        :class="{
                            'text-yellow-500': value?.markForUpload,
                        }"
                        v-tooltip="
                            value?.markForUpload
                                ? __('This is a new file marked for upload')
                                : value.file_name
                        "
                        @click.prevent.stop="
                            () => copyToClipboard(value.file_name)
                        "
                    >
                        {{
                            value?.markForUpload
                                ? __("This is a new file marked for upload")
                                : value.file_name
                        }}
                    </b>
                    <p>{{ humanFileSize(value.size) }}</p>
                </div>

                <Dropdown
                    placement="bottom-start"
                    class="btn-block place-self-start"
                >
                    <DropdownTrigger
                        :show-arrow="false"
                        class="h-7 w-7 bg-gray-100 dark:bg-gray-900"
                    >
                        <Icon type="menu" width="16" solid />
                    </DropdownTrigger>

                    <template #menu>
                        <DropdownMenu>
                            <div class="flex flex-col py-1">
                                <DropdownMenuItem
                                    as="button"
                                    class="flex py-1 hover:bg-gray-100"
                                    @click.prevent="showDetail"
                                >
                                    <Icon
                                        class="flex-shrink-0"
                                        type="eye"
                                        width="20"
                                        height="20"
                                    />
                                    <span class="ml-2">
                                        {{ __("Details") }}
                                    </span>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    v-if="
                                        !readonly &&
                                        field.customPropertiesFields
                                    "
                                    as="button"
                                    class="flex py-1 hover:bg-gray-100"
                                    @click.prevent="editCustomProperties"
                                >
                                    <Icon
                                        class="flex-shrink-0"
                                        type="pencil"
                                        width="20"
                                        height="20"
                                    />
                                    <span class="ml-2">
                                        {{ __("Properties") }}
                                    </span>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    :href="value.conversion.original"
                                    as="external"
                                    target="_blank"
                                    class="flex py-1 hover:bg-gray-100"
                                >
                                    <Icon
                                        class="flex-shrink-0"
                                        type="external-link"
                                        width="20"
                                        height="20"
                                    />
                                    <span class="ml-2">
                                        {{ __("Open") }}
                                    </span>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    :href="value.conversion.original"
                                    as="external"
                                    target="_blank"
                                    download
                                    class="flex py-1 hover:bg-gray-100"
                                >
                                    <Icon
                                        class="flex-shrink-0"
                                        type="download"
                                        width="20"
                                        height="20"
                                    />
                                    <span class="ml-2">
                                        {{ __("Download") }}
                                    </span>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    as="button"
                                    class="flex py-1 hover:bg-gray-100"
                                    @click.prevent="
                                        () =>
                                            copyToClipboard(
                                                value.conversion.original
                                            )
                                    "
                                >
                                    <Icon
                                        class="flex-shrink-0"
                                        type="clipboard-copy"
                                        width="20"
                                        height="20"
                                    />
                                    <span class="ml-2">
                                        {{ __("Copy Url") }}
                                    </span>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    v-if="!readonly && !value?.markForUpload"
                                    as="button"
                                    class="flex py-1 hover:bg-gray-100"
                                    :destructive="true"
                                    @click.prevent="regenerateFile"
                                >
                                    <Icon
                                        class="flex-shrink-0"
                                        type="refresh"
                                        width="20"
                                        height="20"
                                    />
                                    <span class="ml-2">
                                        {{ __("Regenerate") }}
                                    </span>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    v-if="!readonly"
                                    as="button"
                                    class="flex py-1 hover:bg-gray-100"
                                    :destructive="true"
                                    @click.prevent="deleteFile"
                                >
                                    <Icon
                                        class="flex-shrink-0"
                                        type="trash"
                                        width="20"
                                        height="20"
                                    />
                                    <span class="ml-2">
                                        {{ __("Delete") }}
                                    </span>
                                </DropdownMenuItem>
                            </div>
                        </DropdownMenu>
                    </template>
                </Dropdown>
            </div>
        </div>

        <span
            v-if="fileErrors.length > 0"
            class="text-red-500 text-xs flex break-all"
        >
            {{ fileErrors[0][0] }}
        </span>
    </li>
</template>

<script setup>
import dot from "dot-object";
import { computed } from "vue";
import useHumanizeFileSize from "../composables/useHumanizeFileSize";
import useCopyToClipboard from "../composables/useCopyToClipboard";
import useMediaManipulations from "../composables/useMediaManipulations";
import useDotObject from "../composables/useDotObject";

const props = defineProps({
    value: {
        type: Array,
        required: true,
    },
    field: {
        type: Object,
        required: true,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    draggable: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
    },
});

const emit = defineEmits([
    "showDetail",
    "delete",
    "input",
    "editCustomProperties",
]);

const { copyToClipboard } = useCopyToClipboard();
const { humanFileSize } = useHumanizeFileSize();
const { regenerate } = useMediaManipulations();

const { toDotCase } = useDotObject();

const fileErrors = computed(() =>
    Object.values(
        toDotCase(
            dot.pick(
                `${props.field.attribute}.${props.value.id}`,
                props.errors
            ) || {}
        )
    )
);

const editCustomProperties = () => emit("editCustomProperties", props.value.id);
const deleteFile = () => emit("delete", props.value.id);
const showDetail = () => emit("showDetail", props.value);

const regenerateFile = () => {
    const value = props.value;
    value.processing = true;

    emit("input", value);

    regenerate(value.id).finally(() => {
        value.processing = false;

        emit("input", value);
    });
};
</script>

<style>
.overlay-content-bg {
    background-color: rgba(var(--colors-gray-900), 0.85);
}
</style>
