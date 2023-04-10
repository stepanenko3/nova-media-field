export default function useDotObject() {
    function fromDotCase(obj) {
        const result = {};

        for (const objectPath in obj) {
            const parts = objectPath.split('.');

            let target = result;
            while (parts.length > 1) {
                const part = parts.shift();

                if (!part) break;

                target[part] = target[part] || {};
                target = target[part];
            }

            target[parts[0]] = obj[objectPath];
        }

        return result;
    }

    function toDotCase(
        obj,
        target = {},
        prefix = '',
    ) {
        Object.keys(obj).forEach(function (key) {
            if (typeof (obj[key]) === 'object' && !Array.isArray(obj[key])) {
                toDotCase(obj[key], target, prefix + key + '.');
            } else {
                return target[prefix + key] = obj[key];
            }

            return null;
        });

        return target;
    }

    return {
        fromDotCase,
        toDotCase,
    }
}
