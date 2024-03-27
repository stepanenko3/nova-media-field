export default function useMediaManipulations() {
    async function regenerate(id) {
        const response = await fetch(
            `/nova-vendor/stepanenko3/nova-media-field/${id}/regenerate`,
            {
                method: 'POST',
                headers: {
                    "X-CSRF-TOKEN": document.head.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json",
                },
            },
        );

        const data = await response.json();

        if (response.ok && data.success) {
            Nova.success('Media was regenerated!');
        } else {
            Nova.error(data.message);
        }
    };

    return {
        regenerate,
    }
}
