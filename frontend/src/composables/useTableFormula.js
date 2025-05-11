// composables/useTableFormula.js
export function useTableFormula() {
  const calculateFormula = (formula, row) => {
    try {
      let calculableFormula = formula;
      Object.keys(row).forEach(key => {
        calculableFormula = calculableFormula.replace(
          new RegExp(`\\{${key}\\}`, 'g'),
          parseFloat(row[key]) || 0
        );
      });
      const result = new Function(`return ${calculableFormula}`)();
      return isNaN(result) ? 0 : result.toFixed(2);
    } catch (error) {
      return 'Ошибка';
    }
  };

  return {calculateFormula};
}
