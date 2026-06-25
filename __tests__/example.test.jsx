import React from 'react'
import { render, screen } from '@testing-library/react'
import { describe, it, expect } from 'vitest'

describe('Basic Render Test', () => {
  it('renders a simple element and asserts text', () => {
    render(<div>Hello Serv5</div>)
    expect(screen.getByText('Hello Serv5')).toBeDefined()
  })
})
