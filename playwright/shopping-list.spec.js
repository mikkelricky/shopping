// @ts-check
const { test, expect } = require('@playwright/test')

test('Can create shopping list', async ({ page }) => {
  await page.goto('/')

  await page.getByRole('link', { name: 'Create shopping list' }).click()

  await page.getByLabel('Your e-mail address', { exact: true }).fill('test@example.com')
  await page.getByLabel('Shopping list name', { exact: true }).fill('Test')

  await page.getByRole('button', { name: 'Create shopping list' }).click()

  await expect(
    page.getByText('Shopping list created')
  ).toBeVisible()
})
