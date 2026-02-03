/**
 * Block Converter Utility
 *
 * Converts AI-generated block JSON to Gutenberg blocks
 */

import { createBlock } from '@wordpress/blocks';

/**
 * Convert AI response block data to Gutenberg block
 *
 * @param {Object} blockData - Block data from AI response
 * @return {Object} Gutenberg block instance
 */
export function convertToBlock(blockData) {
    if (!blockData || !blockData.blockName) {
        console.error('Invalid block data:', blockData);
        return null;
    }

    const { blockName, attrs = {}, innerBlocks = [] } = blockData;

    // Recursively convert inner blocks
    const convertedInnerBlocks = innerBlocks
        .map((innerBlock) => convertToBlock(innerBlock))
        .filter((block) => block !== null);

    try {
        return createBlock(blockName, attrs, convertedInnerBlocks);
    } catch (error) {
        console.error(`Failed to create block "${blockName}":`, error);
        return null;
    }
}

/**
 * Convert multiple block data objects to Gutenberg blocks
 *
 * @param {Array} blocksData - Array of block data objects
 * @return {Array} Array of Gutenberg block instances
 */
export function convertToBlocks(blocksData) {
    if (!Array.isArray(blocksData)) {
        // If single block object, wrap in array
        if (blocksData && blocksData.blockName) {
            return [convertToBlock(blocksData)].filter(Boolean);
        }
        return [];
    }

    return blocksData.map((blockData) => convertToBlock(blockData)).filter(Boolean);
}

/**
 * Validate block structure before conversion
 *
 * @param {Object} blockData - Block data to validate
 * @return {boolean} Whether the block data is valid
 */
export function validateBlockData(blockData) {
    if (!blockData || typeof blockData !== 'object') {
        return false;
    }

    if (!blockData.blockName || typeof blockData.blockName !== 'string') {
        return false;
    }

    // Validate block name format (namespace/block-name)
    const blockNamePattern = /^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/;
    if (!blockNamePattern.test(blockData.blockName)) {
        return false;
    }

    // Validate inner blocks recursively
    if (blockData.innerBlocks) {
        if (!Array.isArray(blockData.innerBlocks)) {
            return false;
        }
        for (const innerBlock of blockData.innerBlocks) {
            if (!validateBlockData(innerBlock)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Parse AI response and extract block data
 *
 * @param {Object} response - API response object
 * @return {Object|null} Extracted block data or null
 */
export function parseAIResponse(response) {
    if (!response) {
        return null;
    }

    // If response has a block property, use it
    if (response.block) {
        return response.block;
    }

    // If response has blocks array, return first block
    if (response.blocks && Array.isArray(response.blocks)) {
        return response.blocks[0] || null;
    }

    // If response itself is block data
    if (response.blockName) {
        return response;
    }

    return null;
}

export default {
    convertToBlock,
    convertToBlocks,
    validateBlockData,
    parseAIResponse,
};
