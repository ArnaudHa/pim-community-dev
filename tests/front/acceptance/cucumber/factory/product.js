/**
 * Generate a product
 *
 * @param {String} identifier
 * @param {Array} values
 * @param {Boolean} enabled
 * @param {String} family
 * @param {Object} label
 * @param {String} model_type
 * @param {Object} image
 * @param {Array} completenesses
 * @returns {Object}
 */
const createProduct = (
    identifier,
    values = [],
    enabled = true,
    family = 'scanner',
    label = {},
    model_type = 'product',
    image = null,
    completenesses = []
) => {
    return {
        identifier,
        values,
        enabled,
        family,
        meta: {
            label,
            model_type,
            image,
            completenesses
        }
    };
};

const createProductWithLabels = (identifier, labels) => {
    return createProduct(identifier, undefined, undefined, undefined, labels);
};

module.exports = { createProduct, createProductWithLabels };
