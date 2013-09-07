Validation.add('gomage-validate-number',
				'Please use only numbers (0-9) in this field.', function(v) {
					return Validation.get('IsEmpty').test(v)
							|| (!isNaN(parseNumber(v)) && !/^\s+$/
									.test(parseNumber(v)));
				});