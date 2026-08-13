// orzech-theme — JS source
// This file is compiled to assets/js/main.js by `gulp build` / `gulp dev`.
// Add page-specific or site-wide JS here.

(() => {
  const currency = new Intl.NumberFormat('en-CA', {
    style: 'currency',
    currency: 'CAD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

  const wholeCurrency = new Intl.NumberFormat('en-CA', {
    style: 'currency',
    currency: 'CAD',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });

  const calculateMonthlyPayment = (principal, annualRate, months) => {
    const monthlyRate = annualRate / 100 / 12;

    if (monthlyRate === 0) {
      return principal / months;
    }

    return principal * monthlyRate / (1 - Math.pow(1 + monthlyRate, -months));
  };

  const initCalculator = (calculator) => {
    if (calculator.dataset.financeCalculatorReady === 'true') {
      return;
    }

    const range = calculator.querySelector('[data-finance-range]');
    const term = calculator.querySelector('[data-finance-term]');
    const amountOutput = calculator.querySelector('[data-finance-amount]');
    const dailyOutput = calculator.querySelector('[data-finance-daily]');
    const monthlyOutputs = calculator.querySelectorAll('[data-finance-monthly]');

    if (!range || !term || !amountOutput || !dailyOutput || !monthlyOutputs.length) {
      return;
    }

    const update = () => {
      const principal = Number(range.value);
      const months = Number(term.value);
      const apr = Number(calculator.dataset.apr);
      const monthlyPayment = calculateMonthlyPayment(principal, apr, months);
      const dailyPayment = monthlyPayment * 12 / 365;
      const progress = (principal - Number(range.min)) / (Number(range.max) - Number(range.min)) * 100;

      amountOutput.textContent = wholeCurrency.format(principal).replace(/\u00a0/g, '');
      monthlyOutputs.forEach((monthlyOutput) => {
        monthlyOutput.textContent = currency.format(monthlyPayment).replace(/\u00a0/g, '');
      });
      dailyOutput.textContent = currency.format(dailyPayment).replace(/\u00a0/g, '');
      range.style.setProperty('--range-progress', `${Math.max(0, Math.min(100, progress))}%`);
    };

    range.addEventListener('input', update);
    term.addEventListener('change', update);
    calculator.dataset.financeCalculatorReady = 'true';
    update();
  };

  const initCalculators = () => {
    document.querySelectorAll('[data-finance-calculator]').forEach(initCalculator);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalculators);
  } else {
    initCalculators();
  }
})();
