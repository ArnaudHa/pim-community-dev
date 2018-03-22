module.exports = function createLocale(fields) {
    const { code } = fields;
    const regions = { de: 'Germany', fr: 'France', us: 'United States'};
    const languages = { de: 'German', fr: 'French', en: 'English'};
    const [language, region] = code.split('_');

    return Object.assign({
        code,
        region: regions[region],
        label: `${languages[language.toLowerCase()]} (${regions[region]})`,
        language: languages[language.toLowerCase()]
    }, fields);
};

