// @ts-check

/**
 * @type {import('lint-staged').Configuration}
 */
export default {
  "**/*.php": ["composer analyse", "composer format"],
};
