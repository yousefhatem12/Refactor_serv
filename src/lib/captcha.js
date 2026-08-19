export const getCaptchaRequiredMessage = (language = "en") =>
  language === "ar"
    ? "من فضلك أكمل التحقق قبل الإرسال."
    : "Please complete the CAPTCHA before submitting.";

