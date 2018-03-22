function createLabels({
    locales = {},
    addPrefix = false,
    addChangeset = false
}) {
    const labels = {};
    const prefix = addPrefix ? 'label-' : '';

    Object.keys(locales).forEach(locale => {
        const labelName = prefix + locale;
        const value = locales[locale];
        labels[labelName] = addChangeset ? { old: value, 'new': value } : value;
    });

    return labels;
}

module.exports = (
    code = 'blah',
    locales = {
        en_US: 'Association type',
        fr_FR: 'Type association',
        de_DE: 'Blah'
    }
) => {
    const updatedDate = '03/07/2018 09:35 AM';
    const id = 'id for asstype';

    const labels = createLabels({locales});
    const labelSnapshot = createLabels({ locales, addPrefix: true, addChangeset: true });
    const labelChangeset = createLabels({ locales, addPrefix: true });

    return {
        code,
        labels,
        meta: {
            id,
            form: 'pim-association-type-edit-form',
            model_type: 'association_type',
            created: {
                id,
                author: 'system - Removed user',
                resource_id: id,
                snapshot: { code, ...labelSnapshot },
                changeset: {
                    code: { old: '', new: code },
                    ...labelChangeset
                },
                context: null,
                version: 1,
                logged_at: updatedDate,
                pending: false
            },
            updated: {
                id,
                author: 'system - Removed user',
                resource_id: id,
                snapshot: { code, ...labelSnapshot },
                changeset: {
                    code: { old: '', 'new': code },
                    ...labelChangeset
                },
                context: null,
                version: 1,
                logged_at: updatedDate,
                pending: false
            }
        }
    };
};
