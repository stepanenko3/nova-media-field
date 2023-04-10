export default function useMediaManipulations() {
    async function regenerate(id) {
        const res = await Nova.request().post(`/nova-vendor/stepanenko3/nova-media-field/${id}/regenerate`);

        if (res.data.success) {
            Nova.success('Media was regenerated!');
        } else {
            Nova.error(res.data.message);
        }

    };

    return {
        regenerate,
    }
}
