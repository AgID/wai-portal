module.exports = {
  'rules': {
    'at-rule-no-unknown': [true, {
      'ignoreAtRules': ['each', 'extend', 'include', 'mixin', 'use']
    }],
    'scss/at-extend-no-missing-placeholder': null
  },
  'extends': 'stylelint-config-standard-scss',
}
