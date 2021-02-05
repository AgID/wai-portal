module.exports = {
  'rules': {
    'at-rule-no-unknown': [true, {
      'ignoreAtRules': ['each', 'extend', 'include', 'mixin', 'use']
    }]
  },
  'extends': 'stylelint-config-standard',
}
