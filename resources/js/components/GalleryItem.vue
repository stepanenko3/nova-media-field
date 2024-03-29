<template>
    <li class="relative">
        <div
            class="flex md:flex-col"
            :class="{
                'border-transparent': !error,
                'border-red-500': !!error,
                'pointer-events-none': value.processing,
                'cursor-pointer': !value.processing,
            }"
        >
            <div
                class="relative select-none h-24 w-24 md:h-32 lg:h-40 md:w-full flex-shrink-0"
                @click.prevent="showDetail"
            >
                <GalleryPicture :value="value" :field="field" />

                <div
                    v-if="value.processing"
                    v-cloak
                    class="flex items-center justify-center rounded-lg absolute inset-0 bg-white transition-default bg-theme-secondary-900 w-full h-full z-10"
                    style="--tw-bg-opacity: 0.5"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        xml:space="preserve"
                        class="w-20 h-20 text-white animate-spin duration-1000"
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

            <div class="pl-4 md:pl-0 flex-grow">
                <div class="w-full flex items-center pt-2">
                    <div
                        class="md:line-clamp-1 text-gray-900 dark:text-gray-200 font-medium"
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
                    </div>

                    <Dropdown class="flex-shrink-0 ml-auto">
                        <template #trigger>
                            <div
                                class="cursor-pointer flex items-center justify-center w-7 h-7 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900"
                            >
                                <Icon
                                    type="dots-vertical"
                                    width="16"
                                    height="16"
                                />
                            </div>
                        </template>

                        <DropdownMenu @click.prevent="showDetail" as="button">
                            <Icon
                                class="flex-shrink-0"
                                type="eye"
                                width="20"
                                height="20"
                            />
                            <span class="ml-2">
                                {{ __("Details") }}
                            </span>
                        </DropdownMenu>

                        <DropdownMenu
                            v-if="!readonly && field.customPropertiesFields"
                            @click.prevent="editCustomProperties"
                            as="button"
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
                        </DropdownMenu>

                        <DropdownMenu
                            :href="value.conversion.original"
                            as="external"
                            target="_blank"
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
                        </DropdownMenu>

                        <DropdownMenu
                            :href="value.conversion.original"
                            as="external"
                            target="_blank"
                            download
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
                        </DropdownMenu>

                        <DropdownMenu
                            as="button"
                            @click.prevent="
                                () => copyToClipboard(value.conversion.original)
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
                        </DropdownMenu>

                        <DropdownMenu
                            v-if="!readonly && !value?.markForUpload"
                            as="button"
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
                        </DropdownMenu>

                        <DropdownMenu
                            class="text-red-500"
                            v-if="!readonly"
                            as="button"
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
                        </DropdownMenu>
                    </Dropdown>
                </div>
                <p>{{ humanFileSize(value.size) }}</p>

                <span
                    v-if="fileErrors.length > 0"
                    class="text-red-500 text-xs flex break-all"
                >
                    {{ fileErrors[0][0] }}
                </span>
            </div>
        </div>
    </li>
</template>

<script setup lang="ts">
import dot from "dot-object";
import { computed } from "vue";
import useHumanizeFileSize from "../composables/useHumanizeFileSize";
import useCopyToClipboard from "../composables/useCopyToClipboard";
import useMediaManipulations from "../composables/useMediaManipulations";
import useDotObject from "../composables/useDotObject";
import Dropdown from "./Elements/Dropdown.vue";
import DropdownMenu from "./Elements/DropdownMenu.vue";

const props = withDefaults(
    defineProps<{
        value: any;
        field: any;
        readonly?: boolean;
        draggable?: boolean;
        errors?: any;
    }>(),
    {
        readonly: false,
        draggable: false,
        errors: null,
    }
);

const emit = defineEmits<{
    (e: "showDetail", value: any): void;
    (e: "delete", value: any): void;
    (e: "input", value: any): void;
    (e: "editCustomProperties", value: any): void;
}>();

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
