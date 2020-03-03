module.exports = {
  'rules': {
    'at-rule-no-unknown': [true, {
      'ignoreAtRules': ['each', 'extend', 'include', 'mixin']
    }]
  },
  'extends': 'stylelint-config-standard',
}
