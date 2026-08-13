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
    const presetButtons = calculator.querySelectorAll('[data-finance-preset]');

    if (!range || !term || !amountOutput || !dailyOutput || !monthlyOutputs.length) {
      return;
    }

    const minimumAmount = Number(calculator.dataset.amountMin);
    const maximumAmount = Number(calculator.dataset.amountMax);
    const amountStep = Number(calculator.dataset.amountStep);

    const positionToAmount = (position) => {
      if (maximumAmount === minimumAmount) {
        return minimumAmount;
      }

      const rawAmount = minimumAmount * Math.pow(maximumAmount / minimumAmount, position / 100);
      const roundedAmount = Math.round(rawAmount / amountStep) * amountStep;
      return Math.max(minimumAmount, Math.min(maximumAmount, roundedAmount));
    };

    const amountToPosition = (amount) => {
      if (maximumAmount === minimumAmount) {
        return 0;
      }

      const clampedAmount = Math.max(minimumAmount, Math.min(maximumAmount, amount));
      return 100 * Math.log(clampedAmount / minimumAmount) / Math.log(maximumAmount / minimumAmount);
    };

    const update = () => {
      const principal = positionToAmount(Number(range.value));
      const months = Number(term.value);
      const apr = Number(calculator.dataset.apr);
      const monthlyPayment = calculateMonthlyPayment(principal, apr, months);
      const dailyPayment = monthlyPayment * 12 / 365;
      const progress = Number(range.value);

      amountOutput.textContent = wholeCurrency.format(principal).replace(/\u00a0/g, '');
      monthlyOutputs.forEach((monthlyOutput) => {
        monthlyOutput.textContent = currency.format(monthlyPayment).replace(/\u00a0/g, '');
      });
      dailyOutput.textContent = currency.format(dailyPayment).replace(/\u00a0/g, '');
      range.style.setProperty('--range-progress', `${Math.max(0, Math.min(100, progress))}%`);
      range.setAttribute('aria-valuetext', wholeCurrency.format(principal).replace(/\u00a0/g, ''));
      presetButtons.forEach((button) => {
        button.setAttribute('aria-pressed', String(Number(button.dataset.financePreset) === principal));
      });
    };

    range.addEventListener('input', update);
    term.addEventListener('change', update);
    presetButtons.forEach((button) => {
      button.addEventListener('click', () => {
        range.value = amountToPosition(Number(button.dataset.financePreset));
        update();
        range.focus({ preventScroll: true });
      });
    });
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
