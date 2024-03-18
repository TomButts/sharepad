const isValidEmail = function(email) {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
};

export const emailValidationMixin = {
  methods: {
    $isValidEmail: isValidEmail
  }
};
  