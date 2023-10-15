// @ts-check
const { test, expect } = require('@playwright/test')

test('Can create shopping list', async ({ playwright, page }) => {
  const mailServer = await playwright.request.newContext({
    baseURL: 'http://mail:8025'
  })

  expect((await mailServer.delete('/api/v1/messages')).ok()).toBeTruthy()

  let mails = await (await mailServer.get('/api/v1/messages')).json()
  expect(mails.messages).toHaveLength(0)

  await page.goto('/')

  await page.getByRole('link', { name: 'Create shopping list', exact: true }).click()

  await page.getByLabel('Your e-mail address', { exact: true }).fill('user@example.com')

  await page.getByLabel('Shopping list name', { exact: true }).fill('My shopping list')

  await page.getByLabel('Shopping list description', { exact: true }).fill('This is a test shopping list.')

  await page.getByRole('button', { name: 'Create shopping list', exact: true }).click()

  await expect(
    page.getByText('Your shopping list My shopping list has been created')
  ).toBeVisible()

  mails = await (await mailServer.get('/api/v1/messages')).json()
  expect(mails.messages).toHaveLength(1)

  mails = await (await mailServer.get('/api/v1/search', {
    params: {
      query: 'to:user@example.com'
    }
  })).json()

  expect(mails.messages).toHaveLength(1)
  const mail = await (await mailServer.get('/api/v1/message/' + mails.messages[0].ID)).json()

  const match = /Your shopping list (.+) is ready at (http.*)/.exec(mail.Text)
  const [, , url] = match

  await page.goto(url)

  await expect(
    page.getByText('My shopping list')
  ).toBeVisible()

  await expect(
    page.getByText('Woohoo! No not done items.')
  ).toBeVisible()

  await page.getByPlaceholder('Add item', { exact: true }).fill('2 l milk')
  await page.getByRole('button', { name: 'Add item', exact: true }).click()

  await expect(
    page.getByText('Item milk added')
  ).toBeVisible()

  await expect(
    page.getByText('milk (2 l)')
  ).toBeVisible()

  await page
    .getByRole('button', { name: 'Mark milk as done' })
    .click()

  await expect(
    page.getByText('Item milk marked as done')
  ).toBeVisible()

  await expect(
    page.getByText('milk (2 l)')
  ).not.toBeVisible()

  await page
    .getByRole('link', { name: 'Show one done item' })
    .click()

  await expect(
    page.getByText('milk (2 l)')
  ).toBeVisible()

  await page
    .getByRole('button', { name: 'Mark milk as not done' })
    .click()

  await expect(
    page.getByText('Item milk marked as not done')
  ).toBeVisible()
})
